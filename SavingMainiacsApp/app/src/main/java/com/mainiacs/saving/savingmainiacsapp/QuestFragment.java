package com.mainiacs.saving.savingmainiacsapp;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.design.widget.TabLayout;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.ViewPager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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

    private static final String URL_GET_ACTIVE_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_active_quests.php?";
    private static final String URL_GET_PENDING_QUESTS = "  https://abnet.ddns.net/mucoftware/remote/get_user_pending_quests.php?";
    private static final String URL_GET_REJECTED_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_rejected_quests.php?";
    private static final String URL_GET_COMPLETED_QUESTS = "https://abnet.ddns.net/mucoftware/remote/get_user_rewarded_quests.php?";
    private static final String URL_LEAVE_QUEST = "https://abnet.ddns.net/mucoftware/remote/leave_quest.php?";
    private static final String URL_COMPLETE_QUEST = "https://abnet.ddns.net/mucoftware/remote/complete_quest.php?";

    public static final int QUEST_STATUS_ACTIVE = 0;
    public static final int QUEST_STATUS_PENDING = 1;
    public static final int QUEST_STATUS_REJECTED = 2;
    public static final int QUEST_STATUS_COMPLETED = 3;

    private static final int NUM_QUEST_TABS = 4;

    private static final String ARG_USERNAME = "username";
    private static final String ARG_PASSWORD = "password";

    private String username;
    private String password;

    private ArrayList<UserQuestInfo> activeQuests;
    private ArrayList<UserQuestInfo> pendingQuests;
    private ArrayList<UserQuestInfo> completedQuests;
    private ArrayList<UserQuestInfo> rejectedQuests;

    private ViewPager questPager;
    private TabLayout tabs;

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

        tabs = (TabLayout) view.findViewById(R.id.quests_tabs);
        tabs.setupWithViewPager(questPager);

        getAllQuests();

        return view;
    }

    private void populatePage() {

        ViewPagerAdapter adapter = new ViewPagerAdapter(getChildFragmentManager());
        adapter.addFragment(UserQuestInfoFragment.newInstance(activeQuests, QUEST_STATUS_ACTIVE), "Active");
        adapter.addFragment(UserQuestInfoFragment.newInstance(pendingQuests, QUEST_STATUS_PENDING), "Pending");
        adapter.addFragment(UserQuestInfoFragment.newInstance(rejectedQuests, QUEST_STATUS_REJECTED), "Rejected");
        adapter.addFragment(UserQuestInfoFragment.newInstance(completedQuests, QUEST_STATUS_COMPLETED), "Completed");

        questPager.setAdapter(adapter);

        // Set icons for each tab
        int[] tabIcons = {R.drawable.quest_active
                , R.drawable.quest_pending
                , R.drawable.quest_rejected
                , R.drawable.quest_completed};

        for (int i = 0; i < NUM_QUEST_TABS; i++) {
            tabs.getTabAt(i).setIcon(tabIcons[i]);
        }
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
            return null;
        }
    }

    private void getAllQuests() {
        // Start chain of queries to get all quests of different statuses
        getActiveQuests();
    }

    private void getActiveQuests() {
        final RequestQueue queue = Volley.newRequestQueue(getContext());
        String url = URL_GET_ACTIVE_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        activeQuests.clear();

                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            String questName = quest.getString("QuestName");
                            String questDescription = quest.getString("QuestDescription");
                            String charityName = quest.getString("CharityName");

                            activeQuests.add(new UserQuestInfo(userQuestId, questName, questDescription, charityName, rewardAmount, date));
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
        String url = URL_GET_PENDING_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        pendingQuests.clear();

                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            String questName = quest.getString("QuestName");
                            String questDescription = quest.getString("QuestDescription");
                            String charityName = quest.getString("CharityName");

                            pendingQuests.add(new UserQuestInfo(userQuestId, questName, questDescription, charityName, rewardAmount, date));
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
        String url = URL_GET_REJECTED_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        rejectedQuests.clear();

                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            String questName = quest.getString("QuestName");
                            String questDescription = quest.getString("QuestDescription");
                            String charityName = quest.getString("CharityName");

                            rejectedQuests.add(new UserQuestInfo(userQuestId, questName, questDescription, charityName, rewardAmount, date));
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
        String url = URL_GET_COMPLETED_QUESTS + "user=" + username + "&password=" + password;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        completedQuests.clear();

                        JSONArray questList = jsonObject.getJSONArray("results");
                        for (int i = 0; i < questList.length(); i++) {
                            JSONObject quest = questList.getJSONObject(i);

                            int userQuestId = quest.getInt("ActiveID");
                            int rewardAmount = quest.getInt("RewardAmount");
                            String date = quest.getString("AcceptDate");

                            String questName = quest.getString("QuestName");
                            String questDescription = quest.getString("QuestDescription");
                            String charityName = quest.getString("CharityName");

                            completedQuests.add(new UserQuestInfo(userQuestId, questName, questDescription, charityName, rewardAmount, date));
                        }

                        // Set up viewpager after collecting all the data
                        populatePage();

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

    private void leaveActiveQuest(int activeQuestId) {
        final RequestQueue queue = Volley.newRequestQueue(getContext());
        String url = URL_LEAVE_QUEST + "user=" + username + "&password=" + password + "&activequestid=" + activeQuestId;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        // TODO: Refresh list of active quests

                    } else {
                        Toast.makeText(getContext(), "Failed to leave quest.", Toast.LENGTH_LONG).show();
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

    private void completeActiveQuest(int activeQuestId) {
        final RequestQueue queue = Volley.newRequestQueue(getContext());
        String url = URL_COMPLETE_QUEST + "user=" + username + "&password=" + password + "&activequestid=" + activeQuestId;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                try {
                    if (jsonObject.getInt("success") == 1) {
                        // TODO: Refresh list of active quests

                    } else {
                        Toast.makeText(getContext(), "Failed to mark quest as complete.", Toast.LENGTH_LONG).show();
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
