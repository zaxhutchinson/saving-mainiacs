package com.mainiacs.saving.savingmainiacsapp;

import android.content.Intent;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.support.design.widget.NavigationView;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Base64;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.ImageView;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener {

    private static String USER_PICTURE_URL = "https://abnet.ddns.net/mucoftware/remote/get_user_picture.php?userid=";

    DataManager dm;
    UserProfile user;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.setDrawerListener(toggle);
        toggle.syncState();

        NavigationView navigationView = (NavigationView) findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        // Select the first item on list
        navigationView.getMenu().getItem(0).setChecked(true);

        initializeApp();

    }

    public void initializeApp() {

        dm = getIntent().getParcelableExtra("DataManager");
        user = dm.userProfile;

        GetUserPicture();
        PopulateUserData();

    }

    void PopulateUserData() {
        TextView daySteps = (TextView)findViewById(R.id.userDaySteps);
        TextView coins = (TextView)findViewById(R.id.userCoins);

        daySteps.setText(Integer.toString(user.DaySteps()));
        coins.setText(Integer.toString(user.Coins()));
    }

    void GetUserPicture() {
        final RequestQueue queue = Volley.newRequestQueue(this);

        String url = USER_PICTURE_URL + Integer.toString(user.ID());

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                //System.out.println(jsonObject.toString());
                try {
                    if(jsonObject.getInt("success")==1) {

                        String base64encodedPic = jsonObject.getString("data");
                        byte[] picbyes = Base64.decode(base64encodedPic, Base64.DEFAULT);
                        user.Picture = BitmapFactory.decodeByteArray(picbyes, 0, picbyes.length);
                        ImageView pic = (ImageView)findViewById(R.id.userpic);
                        pic.setImageBitmap(user.Picture);
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

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @SuppressWarnings("StatementWithEmptyBody")
    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        // Handle navigation view item clicks here.
        int id = item.getItemId();

        switch (id) {
            case R.id.nav_tracker:
                break;
            case R.id.nav_leaderboard:
                break;
            case R.id.nav_quests:
                break;
            case R.id.nav_maps:
                Intent intent = new Intent(this, MapsActivity.class);
                intent.putExtra("DataManager", dm);
                startActivity(intent);
                break;
            case R.id.nav_user_settings:
                break;
            case R.id.nav_sign_off:
                break;
            default:
                break;

        }

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }
}
