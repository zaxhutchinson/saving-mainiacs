package com.mainiacs.saving.savingmainiacsapp;

import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Base64;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class UserProfileActivity extends AppCompatActivity {

    private static String USER_PICTURE_URL = "https://abnet.ddns.net/mucoftware/remote/get_user_picture.php?userid=";

    DataManager dm;
    UserProfile user;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_profile);

        dm = getIntent().getParcelableExtra("DataManager");
        user = dm.userProfile;

    }

    @Override
    protected void onResume() {
        super.onResume();

        GetUserPicture();
        PopulateUserData();

    }

    void PopulateUserData() {
        TextView name = (TextView)findViewById(R.id.username);
        TextView email = (TextView)findViewById(R.id.userEmail);
        TextView daySteps = (TextView)findViewById(R.id.userDaySteps);
        TextView monthSteps = (TextView)findViewById(R.id.userMonthSteps);
        TextView totalSteps = (TextView)findViewById(R.id.userTotalSteps);
        TextView coins = (TextView)findViewById(R.id.userCoins);
        TextView totalCoins = (TextView)findViewById(R.id.userTotalCoins);
        TextView latlong = (TextView)findViewById(R.id.userLatLong);


        name.setText(user.LoginName());
        email.setText(user.Email());
        daySteps.setText(Integer.toString(user.DaySteps()));
        monthSteps.setText(Integer.toString(user.MonthSteps()));
        totalSteps.setText(Integer.toString(user.TotalSteps()));
        coins.setText(Integer.toString(user.Coins()));
        totalCoins.setText(Integer.toString(user.TotalCoins()));
        latlong.setText(Double.toString(user.Latitude()) + ", " + Double.toString(user.Longitude()));

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
}
