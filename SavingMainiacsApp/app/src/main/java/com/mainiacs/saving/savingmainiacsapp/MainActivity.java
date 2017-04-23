package com.mainiacs.saving.savingmainiacsapp;

import android.content.Intent;
import android.provider.ContactsContract;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

public class MainActivity extends AppCompatActivity {

    Button btnGoToMap;
    Button btnGoToProfile;
    DataManager dm;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        AppInit();

        btnGoToMap = (Button)findViewById(R.id.button2);
        btnGoToMap.setOnClickListener(new View.OnClickListener() {
            public void onClick(View view) {
                StartMapActivity(view);
            }
        });

        btnGoToProfile = (Button)findViewById(R.id.buttonUserProfile);
        btnGoToProfile.setOnClickListener(new View.OnClickListener() {
            public void onClick(View view) {
                StartUserProfileActivity(view);
            }
        });


    }

    public void AppInit() {

        dm = getIntent().getParcelableExtra("DataManager");
    }

    public void StartMapActivity(View view) {
        Intent intent = new Intent(this, MapsActivity.class);
        intent.putExtra("DataManager",dm);
        startActivity(intent);
    }

    public void StartUserProfileActivity(View view) {
        Intent intent = new Intent(this, UserProfileActivity.class);
        intent.putExtra("DataManager",dm);
        startActivity(intent);
    }
}
