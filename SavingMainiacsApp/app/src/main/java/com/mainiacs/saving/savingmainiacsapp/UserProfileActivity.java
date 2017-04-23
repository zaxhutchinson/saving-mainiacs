package com.mainiacs.saving.savingmainiacsapp;

import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.TextView;

public class UserProfileActivity extends AppCompatActivity {

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

        PopulateUserData();

    }

    void PopulateUserData() {
        TextView name = (TextView)findViewById(R.id.username);
        TextView email = (TextView)findViewById(R.id.userEmail);
        TextView daySteps = (TextView)findViewById(R.id.userDaySteps);
        TextView monthSteps = (TextView)findViewById(R.id.userMonthSteps);
        TextView totalSteps = (TextView)findViewById(R.id.userTotalSteps);

        name.setText(user.LoginName());
        email.setText(user.Email());
        daySteps.setText(Integer.toString(user.DaySteps()));
        monthSteps.setText(Integer.toString(user.MonthSteps()));
        totalSteps.setText(Integer.toString(user.TotalSteps()));
    }
}
