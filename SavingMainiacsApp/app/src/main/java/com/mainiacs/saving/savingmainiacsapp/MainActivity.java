package com.mainiacs.saving.savingmainiacsapp;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.support.design.widget.NavigationView;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

// Tutorial for navigation drawer: http://www.androidhive.info/2013/11/android-sliding-menu-using-navigation-drawer/

public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener
        , ProfileFragment.OnFragmentInteractionListener
        , LeaderBoardFragment.OnFragmentInteractionListener
        , QuestFragment.OnFragmentInteractionListener
        , SettingsFragment.OnFragmentInteractionListener
        , UserQuestInfoFragment.OnListFragmentInteractionListener {

    private static String PROFILE_STRING = "https://abnet.ddns.net/mucoftware/remote/get_user.php?";
    private static String SEND_STEP_URL = "https://abnet.ddns.net/mucoftware/remote/update_user.php?";

    private static final String TAG_HOME = "home";
    private static final String TAG_QUESTS = "quests";
    private static final String TAG_LEADERBOARD = "leaderboard";
    private static final String TAG_SETTINGS = "settings";
    private static final String[] activityTitles = {"Status", "", "My Quests", "Leaderboard", "Settings", ""};

    private NavigationView navigationView;
    private DrawerLayout drawer;
    private String currentTag;
    private int navItemIndex;
    private Handler fragmentSwitchHandler;
    private boolean shouldLoadHomeFragOnBackPress = true;

    DataManager dm;
    UserProfile user = null;

    private String username, password;

    StepCounterService stepCounterService;
    Handler sendHandler;
    Runnable dbSender;

    public void onFragmentInteraction(Uri uri) {
        return;
    }
    public void onListFragmentInteraction(UserQuestInfo info) {
        return;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.setDrawerListener(toggle);
        toggle.syncState();

        navigationView = (NavigationView) findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        fragmentSwitchHandler = new Handler();
        navItemIndex = 0;
        currentTag = TAG_HOME;


        initializeApp();

        RequestUserProfile();

        SetUpStepSensor();

    }

    @Override
    protected void onPause() {
        super.onPause();


    }

    @Override
    protected void onResume() {
        super.onResume();



    }

    public void initializeApp() {

        dm = new DataManager();

        // Save credentials to refresh profile data later
        username = getIntent().getStringExtra(LoginActivity.TAG_USERNAME);
        password = getIntent().getStringExtra(LoginActivity.TAG_PASSWORD);
    }



    public void RequestUserProfile() {
        final RequestQueue queue = Volley.newRequestQueue(this);
        String url = PROFILE_STRING + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                System.out.println(jsonObject.toString());
                try {
                    if (jsonObject.getInt("success") == 1) {

                        dm.userProfile = new UserProfile(jsonObject);
                        dm.userProfile.UserName(username);
                        dm.userProfile.Password(password);

                        user = dm.userProfile;

                        // Load profile fragment after completing request
                        loadHomeFragment();

                    } else {
                        Toast.makeText(getApplicationContext(), "Failed to get profile.", Toast.LENGTH_LONG).show();
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

    void SetUpStepSensor() {
        /* TODO: Uncomment this after the service no longer crashes app */
        stepCounterService = new StepCounterService(user, getApplicationContext());

        if(stepCounterService.stepCounterActive) {

            sendHandler = new Handler();
//        sendHandler.postDelayed(new Runnable() {
//
//            @Override
//            public void run() {
//                SendUpdateToDB();
//            }
//        }, 1000);//300000);

            dbSender = new Runnable() {

                @Override
                public void run() {
                    SendUpdateToDB();
                    sendHandler.postDelayed(this, 5000);
                }
            };
            dbSender.run();
        }
    }

    void SendUpdateToDB() {
        if(user != null) {
            //user=helpfulguy78&password=helpfulguy78&lat=1&long=1&steps=117
            String url = SEND_STEP_URL + "user=" + user.UserName() +
                    "&password=" + user.Password() +
                    "&lat=" + Double.toString(user.Latitude()) +
                    "&long=" + Double.toString(user.Longitude()) +
                    "&steps=" + Integer.toString(user.TempSteps());

            final RequestQueue queue = Volley.newRequestQueue(this);

            JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                    Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
                @Override
                public void onResponse(JSONObject jsonObject) {
                    System.out.println(jsonObject.toString());
                    try {
                        if (jsonObject.getInt("success") == 1) {

                            //RequestUserProfile(queue);
                            Toast.makeText(getApplicationContext(), Integer.toString(user.TempSteps()), Toast.LENGTH_LONG).show();

                        } else {
                            //mEmailView.setError("Error logging in.");
                            //focusView.requestFocus();
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

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
            return;
        }

        // Load home fragment if user is on another menu
        if (shouldLoadHomeFragOnBackPress) {
            if (navItemIndex != 0) {
                navItemIndex = 0;
                currentTag = TAG_HOME;
                loadHomeFragment();
                return;
            }
        }

        super.onBackPressed();
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
        int id = item.getItemId();

        //Check to see which item was being clicked and perform appropriate action
        switch (id) {
            case R.id.nav_tracker:
                navItemIndex = 0;
                currentTag = TAG_HOME;
                break;
            case R.id.nav_maps:
                navItemIndex = 1;
                Intent mapActivityIntent = new Intent(this, MapsActivity.class);
                mapActivityIntent.putExtra("DataManager", dm);
                startActivity(mapActivityIntent);
                drawer.closeDrawers();
                return true;
            case R.id.nav_quests:
                navItemIndex = 2;
                currentTag = TAG_QUESTS;
                break;
            case R.id.nav_leaderboard:
                navItemIndex = 3;
                currentTag = TAG_LEADERBOARD;
                break;
            case R.id.nav_user_settings:
                navItemIndex = 4;
                currentTag = TAG_SETTINGS;
                break;
            case R.id.nav_sign_off:
                navItemIndex = 5;
                Intent loginActivityIntent = new Intent(this, LoginActivity.class);
                startActivity(loginActivityIntent);
                finish();
                return true;
            default:
                navItemIndex = 0;
                currentTag = TAG_HOME;
        }

        item.setChecked(true);

        loadHomeFragment();

        return true;
    }

    private void loadHomeFragment() {
        // selecting appropriate nav menu item
        selectNavMenu();

        // set toolbar title
        setToolbarTitle();

        // if user select the current navigation menu again, don't do anything
        // just close the navigation drawer
        if (getSupportFragmentManager().findFragmentByTag(currentTag) != null) {
            drawer.closeDrawers();
            return;
        }

        // Sometimes, when fragment has huge data, screen seems hanging
        // when switching between navigation menus
        // So using runnable, the fragment is loaded with quest_rejected fade effect
        // This effect can be seen in GMail app
        Runnable mPendingRunnable = new Runnable() {
            @Override
            public void run() {
                // update the main content by replacing fragments
                Fragment fragment = getHomeFragment();
                FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
                fragmentTransaction.setCustomAnimations(android.R.anim.fade_in,
                        android.R.anim.fade_out);
                fragmentTransaction.replace(R.id.fragment_frame, fragment, currentTag);
                fragmentTransaction.commitAllowingStateLoss();
            }
        };

        // If mPendingRunnable is not null, then add to the message queue
        if (mPendingRunnable != null) {
            fragmentSwitchHandler.post(mPendingRunnable);
        }

        //Closing drawer on item click
        drawer.closeDrawers();

        // refresh toolbar menu
        invalidateOptionsMenu();
    }

    private void setToolbarTitle() {
        getSupportActionBar().setTitle(activityTitles[navItemIndex]);
    }

    private void selectNavMenu() {
        navigationView.getMenu().getItem(navItemIndex).setChecked(true);
    }

    private Fragment getHomeFragment() {
        switch (navItemIndex) {
            case 0:
                ProfileFragment profileFragment = ProfileFragment.newInstance(dm);
                return profileFragment;
            case 2:
                QuestFragment questFragment = QuestFragment.newInstance(username, password);
                return questFragment;
            case 3:
                LeaderBoardFragment leaderBoardFragment = new LeaderBoardFragment();
                return leaderBoardFragment;
            case 4:
                SettingsFragment settingsFragment = new SettingsFragment();
                return settingsFragment;
            default:
                return new ProfileFragment();
        }
    }
}
