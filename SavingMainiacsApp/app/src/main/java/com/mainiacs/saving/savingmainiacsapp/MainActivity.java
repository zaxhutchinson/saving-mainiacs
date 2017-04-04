package com.mainiacs.saving.savingmainiacsapp;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

public class MainActivity extends AppCompatActivity {

    Button btnGoToMap;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        btnGoToMap = (Button)findViewById(R.id.button2);
        btnGoToMap.setOnClickListener(new View.OnClickListener() {
            public void onClick(View view) {
                StartMapActivity(view);
            }
        });

    }

    public void StartMapActivity(View view) {
        Intent intent = new Intent(this, MapsActivity.class);
        startActivity(intent);
    }
}
