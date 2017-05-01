package com.mainiacs.saving.savingmainiacsapp;

import android.content.Context;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.support.design.widget.TabLayout;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Adapter;
import android.widget.SimpleAdapter;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class QuestFragment extends Fragment {

    private static final String URL_ACTIVE_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_active_quests.php?";
    private static final String URL_PENDING_QUESTS = "  https://abnet.ddns.net/mucoftware/remote/get_user_pending_quests.php?";
    private static final String URL_REJECTED_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_rejected_quests.php?";
    private static final String URL_COMPLETED_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_rewarded_quests.php?";

    private static final String ARG_USERNAME = "username";
    private static final String ARG_PASSWORD = "password";

    private String username;
    private String password;

    private ArrayList<UserQuestInfo> activeQuests;
    private ArrayList<UserQuestInfo> pendingQuests;
    private ArrayList<UserQuestInfo> completedQuests;
    private ArrayList<UserQuestInfo> rejectedQuests;

    private ViewPager questPager;

    private OnFragmentInteractionListener mListener;

    public QuestFragment() {
        // Required empty public constructor
    }

    public static QuestFragment newInstance(String param1, String param2) {
        QuestFragment fragment = new QuestFragment();
        Bundle args = new Bundle();
        args.putString(ARG_USERNAME, param1);
        args.putString(ARG_PASSWORD, param2);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            username = getArguments().getString(ARG_USERNAME);
            password = getArguments().getString(ARG_PASSWORD);
        }
        activeQuests = new ArrayList<>();
        pendingQuests = new ArrayList<>();
        completedQuests = new ArrayList<>();
        rejectedQuests = new ArrayList<>();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_quest, container, false);
        questPager = (ViewPager) view.findViewById(R.id.quests_pager);

        TabLayout tabs = (TabLayout) view.findViewById(R.id.quests_tabs);
        tabs.setupWithViewPager(questPager);

        getActiveQuests();

        return view;
    }

    private ViewPagerAdapter buildAdapter() {
        ViewPagerAdapter adapter = new ViewPagerAdapter(getChildFragmentManager());
        adapter.addFragment(UserQuestInfoFragment.newInstance(activeQuests), "Active");
        adapter.addFragment(UserQuestInfoFragment.newInstance(pendingQuests), "Pending");
        adapter.addFragment(UserQuestInfoFragment.newInstance(rejectedQuests), "Rejected");
        adapter.addFragment(UserQuestInfoFragment.newInstance(completedQuests), "Completed");
        return adapter;
    }

    private class ViewPagerAdapter extends FragmentPagerAdapter {
        private final List<Fragment> mFragmentList = new ArrayList<>();
        private final List<String> mFragmentTitleList = new ArrayList<>();

        public ViewPagerAdapter(FragmentManager manager) {
            super(manager);
        }

        @Override
        public Fragment getItem(int position) {
            return mFragmentList.get(position);
        }

        @Override
        public int getCount() {
            return mFragmentList.size();
        }

        public void addFragment(Fragment fragment, String title) {
            mFragmentList.add(fragment);
            mFragmentTitleList.add(title);
        }

        @Override
        public CharSequence getPageTitle(int position) {
            return mFragmentTitleList.get(position);
        }
    }

    private void getActiveQuests() {
        final RequestQueue queue = Volley.newRequestQueue(getContext());
        String url = URL_ACTIVE_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int questId = quest.getInt("QuestID");
                            int charityId = quest.getInt("CharityID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            activeQuests.add(new UserQuestInfo(userQuestId, questId, charityId, rewardAmount, date));
                        }
                    } else {
                        Toast.makeText(getContext(), "Failed to get active quests.", Toast.LENGTH_LONG).show();
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
        getPendingQuests(queue);
    }

    private void getPendingQuests(RequestQueue queue) {
        String url = URL_PENDING_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int questId = quest.getInt("QuestID");
                            int charityId = quest.getInt("CharityID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            pendingQuests.add(new UserQuestInfo(userQuestId, questId, charityId, rewardAmount, date));
                        }

                    } else {
                        Toast.makeText(getContext(), "Failed to get pending quests.", Toast.LENGTH_LONG).show();
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
        getRejectedQuests(queue);
    }

    private void getRejectedQuests(RequestQueue queue) {
        String url = URL_REJECTED_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int questId = quest.getInt("QuestID");
                            int charityId = quest.getInt("CharityID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            rejectedQuests.add(new UserQuestInfo(userQuestId, questId, charityId, rewardAmount, date));
                        }

                    } else {
                        Toast.makeText(getContext(), "Failed to get rejected quests.", Toast.LENGTH_LONG).show();
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
        getCompletedQuests(queue);
    }

    private void getCompletedQuests(RequestQueue queue) {
        String url = URL_COMPLETED_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int questId = quest.getInt("QuestID");
                            int charityId = quest.getInt("CharityID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            completedQuests.add(new UserQuestInfo(userQuestId, questId, charityId, rewardAmount, date));
                        }

                        // Set up viewpager after collecting all the data
                        questPager.setAdapter(buildAdapter());

                    } else {
                        Toast.makeText(getContext(), "Failed to get completed quests.", Toast.LENGTH_LONG).show();
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


    // TODO: Rename method, update argument and hook method into UI event
    public void onButtonPressed(Uri uri) {
        if (mListener != null) {
            mListener.onFragmentInteraction(uri);
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnFragmentInteractionListener) {
            mListener = (OnFragmentInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnFragmentInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    /**
     * This interface must be implemented by activities that contain this
     * fragment to allow an interaction in this fragment to be communicated
     * to the activity and potentially other fragments contained in that
     * activity.
     * <p>
     * See the Android Training lesson <a href=
     * "http://developer.android.com/training/basics/fragments/communicating.html"
     * >Communicating with Other Fragments</a> for more information.
     */
    public interface OnFragmentInteractionListener {
        // TODO: Update argument type and name
        void onFragmentInteraction(Uri uri);
    }
}
