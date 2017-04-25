package com.mainiacs.saving.savingmainiacsapp;

import java.io.FileDescriptor;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import android.Manifest;
import android.content.Context;
import android.content.pm.PackageManager;
import android.location.Location;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;

import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.view.View;
import android.widget.Button;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.Api;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.PendingResult;
import com.google.android.gms.common.api.Status;
import com.google.android.gms.location.LocationServices;

import com.google.android.gms.location.places.Places;
import com.google.android.gms.location.places.ui.PlaceAutocomplete;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptor;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class MapsActivity extends FragmentActivity
        implements OnMapReadyCallback,
            GoogleApiClient.ConnectionCallbacks,
            GoogleApiClient.OnConnectionFailedListener,
            GoogleMap.OnMarkerClickListener {


    private static String ALL_CHARITIES_URL = "https://abnet.ddns.net/mucoftware/remote/get_charity_list.php";
    private static String CHARITY_QUEST_URL = "https://abnet.ddns.net/mucoftware/remote/get_quests.php?charityid=";

    private GoogleMap mMap;
    private CameraPosition cameraPosition;
    private GoogleApiClient googleApiClient;

    private static LatLng BANGOR = new LatLng(44.8012, -68.7778);
    private DataManager dm;

    boolean locationPermissionGranted;
    private static final int DEFAULT_ZOOM = 10;
    private static final int PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION = 1;
    private Location lastLocation;

    private Map<Marker, Charity> charityMap;
    private Map<Marker, Quest> questMap;
    private Quest currentQuest;
    private Charity currentCharity;

    private ScrollView charityView;
    private ScrollView questView;

    private Button acceptQuestButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);
        // Obtain the SupportMapFragment and get notified when the map is ready to be used.

        charityMap = new HashMap<Marker, Charity>();
        questMap = new HashMap<Marker, Quest>();



        googleApiClient = new GoogleApiClient.Builder(this)
                .enableAutoManage(this, this)
                .addConnectionCallbacks(this)
                .addApi(LocationServices.API)
                .addApi(Places.GEO_DATA_API)
                .addApi(Places.PLACE_DETECTION_API)
                .build();
        googleApiClient.connect();

        dm = getIntent().getParcelableExtra("DataManager");

        SupportMapFragment mf = (SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map);
        mf.getMapAsync(this);


        charityView = (ScrollView)findViewById(R.id.charityView);
        questView = (ScrollView)findViewById(R.id.questView);

        charityView.setVisibility(View.GONE);
        questView.setVisibility(View.GONE);

        acceptQuestButton = (Button)findViewById(R.id.acceptQuestBtn);
        acceptQuestButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View view) {
                acceptQuest(view);
            }
        });
    }

    private void acceptQuest(View view) {
        final RequestQueue queue = Volley.newRequestQueue(this);

        String url = "https://abnet.ddns.net/mucoftware/remote/accept_quest.php?user=" +
                dm.userProfile.UserName() + "&password=" + dm.userProfile.Password() +
                "&questid=" + currentQuest.ID();

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if(jsonObject.getInt("success")==1) {

                        Context context = getApplicationContext();
                        CharSequence msg = jsonObject.getString("message");
                        int length = Toast.LENGTH_SHORT;
                        Toast.makeText(context, msg, length).show();

                        // Add quest to user profile.

                    }
                    else {

                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {

            }
        }
        );

        queue.add(jsonObjectRequest);
    }

    /**
     * Manipulates the map once available.
     * This callback is triggered when the map is ready to be used.
     * This is where we can add markers or lines, add listeners or move the camera. In this case,
     * we just add a marker near Sydney, Australia.
     * If Google Play services is not installed on the device, the user will be prompted to install
     * it inside the SupportMapFragment. This method will only be triggered once the user has
     * installed Google Play services and returned to the app.
     */
    @Override
    public void onMapReady(GoogleMap googleMap) {
        mMap = googleMap;

        GetCharities();


        requestDeviceLocation();
        updateLocationUI();
        mMap.setOnMarkerClickListener(this);
    }

    public void displayCharityMarkers() {
        for (Charity charity : dm.charities) {
            Marker marker = mMap.addMarker(new MarkerOptions()
                    .position(new LatLng(charity.Latitude(), charity.Longitude()))
                    .title("Charity\n" + charity.Name()));

            charityMap.put(marker, charity);

        }
    }

    private void requestDeviceLocation() {
        if (ContextCompat.checkSelfPermission(this.getApplicationContext(),
                Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED) {
            locationPermissionGranted = true;
        } else {
            ActivityCompat.requestPermissions(this,
                    new String[]{Manifest.permission.ACCESS_FINE_LOCATION},
                    PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION);
        }

        if (locationPermissionGranted) {
            lastLocation = LocationServices.FusedLocationApi
                    .getLastLocation(googleApiClient);
        }

        if (cameraPosition != null) {
            mMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
        } else if (lastLocation != null) {
            mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(
                    new LatLng(lastLocation.getLatitude(),
                            lastLocation.getLongitude()), DEFAULT_ZOOM));
        } else {
            mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(BANGOR, DEFAULT_ZOOM));
            mMap.getUiSettings().setMyLocationButtonEnabled(false);
        }

    }

    @Override
    public void onRequestPermissionsResult(int requestCode,
                                           @NonNull String permissions[],
                                           @NonNull int[] grantResults) {
        locationPermissionGranted = false;
        switch (requestCode) {
            case PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION: {
                // If request is cancelled, the result arrays are empty.
                if (grantResults.length > 0
                        && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                    locationPermissionGranted = true;
                }
            }
        }
        updateLocationUI();
    }

    private void updateLocationUI() {
        if (mMap == null) {
            return;
        }

        /*
         * Request location permission, so that we can get the location of the
         * device. The result of the permission request is handled by a callback,
         * onRequestPermissionsResult.
         */
        if (ContextCompat.checkSelfPermission(this.getApplicationContext(),
                android.Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED) {
            locationPermissionGranted = true;
        } else {
            ActivityCompat.requestPermissions(this,
                    new String[]{android.Manifest.permission.ACCESS_FINE_LOCATION},
                    PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION);
        }

        if (locationPermissionGranted) {
            mMap.setMyLocationEnabled(true);
            mMap.getUiSettings().setMyLocationButtonEnabled(true);
        } else {
            mMap.setMyLocationEnabled(false);
            mMap.getUiSettings().setMyLocationButtonEnabled(false);
            lastLocation = null;
        }
    }

    @Override
    public void onConnected(@Nullable Bundle bundle) {
        SupportMapFragment mapFragment = (SupportMapFragment) getSupportFragmentManager()
                .findFragmentById(R.id.map);
        mapFragment.getMapAsync(this);
    }

    @Override
    public void onConnectionSuspended(int i) {

    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {

    }

    void ShowQuestView() {
        charityView.setVisibility(View.GONE);
        questView.setVisibility(View.VISIBLE);
    }

    void ShowCharityView() {
        questView.setVisibility(View.GONE);
        charityView.setVisibility(View.VISIBLE);
    }


    @Override
    public boolean onMarkerClick(Marker marker) {

        String[] markerTitle = marker.getTitle().split("\n");

        if(markerTitle[0].equals("Charity")) {

            ShowCharityView();

            currentCharity = charityMap.get(marker);

            PopulateCharityInfo(currentCharity);

            //System.out.println(charity.toString());

            for (Marker m : questMap.keySet()) {
                m.remove();
            }

            questMap.clear();

            GetQuests(currentCharity);
        }
        else if(markerTitle[0].equals("Quest")) {

            ShowQuestView();

            currentQuest = questMap.get(marker);

            PopulateQuestInfo(currentQuest);

        }

        return false;
    }

    void PopulateCharityInfo(Charity charity) {
        TextView charityName = (TextView)findViewById(R.id.charityName);
        TextView charityAddr1 = (TextView)findViewById(R.id.charityAddress1);
        TextView charityAddr2 = (TextView)findViewById(R.id.charityAddress2);
        TextView charityPhone = (TextView)findViewById(R.id.charityPhone);

        charityName.setText(charity.Name());
        charityAddr1.setText(charity.Address1());
        charityAddr2.setText(charity.Address2());
        charityPhone.setText(charity.Phone());
    }

    void PopulateQuestInfo(Quest quest) {
        TextView questName = (TextView)findViewById(R.id.questName);
        TextView questDesc = (TextView)findViewById(R.id.questDesc);
        TextView questLocation = (TextView)findViewById(R.id.questLocation);
        TextView questQuantity = (TextView)findViewById(R.id.questQuantity);
        TextView questPayment = (TextView)findViewById(R.id.questPayment);

        questName.setText(quest.Name());
        questDesc.setText(quest.Description());
        questLocation.setText(quest.DropOffLocation());
        questQuantity.setText(Integer.toString(quest.Quantity()));
        questPayment.setText(Integer.toString(quest.Payment()));
    }

    void DisplayQuests(Charity charity) {
        //System.out.println(Integer.toString(charity.GetAllQuests().size()));
        for(Quest quest : charity.GetAllQuests()) {
            Marker m = mMap.addMarker(new MarkerOptions()
                    .position(new LatLng(quest.Latitude(), quest.Longitude()))
                    .title("Quest\n"+quest.Name())
                    .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_BLUE)));
            questMap.put(m,quest);
        }
    }

    public void GetCharities() {

        final RequestQueue queue = Volley.newRequestQueue(this);

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, ALL_CHARITIES_URL, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                //System.out.println(jsonObject.toString());
                try {
                    if(jsonObject.getInt("success")==1) {

                        JSONArray charities = jsonObject.getJSONArray("results");
                        for(int i = 0; i < charities.length(); i++) {
                            dm.charities.add(new Charity(charities.getJSONObject(i)));
                        }
                        displayCharityMarkers();

                    }
                    else {

                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {

            }
        }
        );

        queue.add(jsonObjectRequest);
    }

    public void GetQuests(final Charity charity) {

        String url = CHARITY_QUEST_URL + Integer.toString(charity.ID());

        final RequestQueue queue = Volley.newRequestQueue(this);
        final LinkedList<Quest> all_quests = new LinkedList<Quest>();

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                //System.out.println(jsonObject.toString());
                try {
                    if(jsonObject.getInt("success")==1) {

                        JSONArray quests = jsonObject.getJSONArray("results");

                        charity.GetAllQuests().clear();

                        for(int i = 0; i < quests.length(); i++) {
                            charity.AddQuest(new Quest(quests.getJSONObject(i)));
                        }

                        DisplayQuests(charity);
                    }
                    else {

                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
        }
        }, new Response.ErrorListener() {

            @Override
            public void onErrorResponse(VolleyError volleyError) {

            }
        }
        );

        queue.add(jsonObjectRequest);
    }

}
