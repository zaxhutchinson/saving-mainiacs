package com.mainiacs.saving.savingmainiacsapp;

import android.content.Context;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.os.Parcelable;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.github.mikephil.charting.charts.PieChart;
import com.github.mikephil.charting.data.PieData;
import com.github.mikephil.charting.data.PieDataSet;
import com.github.mikephil.charting.data.PieEntry;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class ProfileFragment extends Fragment {

    private static String PROFILE_STRING = "https://abnet.ddns.net/mucoftware/remote/get_user.php?";
    private static String USER_PICTURE_URL = "https://abnet.ddns.net/mucoftware/remote/get_user_picture.php?userid=";

    private static final String ARG_DATAMANAGER = "dataMangaer";
    private static final int MAX_STEPS = 10000;

    private TextView daySteps;
    private TextView coins;
    private TextView level;
    private PieChart stepChart;
    private ImageView pic;

    private DataManager dm;
    private UserProfile user;


    private OnFragmentInteractionListener mListener;

    public ProfileFragment() {
        // Required empty public constructor
    }

    public static ProfileFragment newInstance(DataManager dataManager) {
        ProfileFragment fragment = new ProfileFragment();
        Bundle args = new Bundle();
        args.putParcelable(ARG_DATAMANAGER, dataManager);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            dm = getArguments().getParcelable(ARG_DATAMANAGER);
        }
        user = dm.userProfile;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_profile, container, false);

        daySteps = (TextView) view.findViewById(R.id.userDaySteps);
        coins = (TextView) view.findViewById(R.id.userCoins);
        level = (TextView) view.findViewById(R.id.userLevel);
        stepChart = (PieChart) view.findViewById(R.id.steps_chart);
        pic = (ImageView) view.findViewById(R.id.userpic);

        if (user != null) PopulateUserData();

        return view;
    }

    void GetUserPicture() {
        final RequestQueue queue = Volley.newRequestQueue(getContext());
        String url = USER_PICTURE_URL + Integer.toString(user.ID());

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject jsonObject) {
                //System.out.println(jsonObject.toString());
                try {
                    if (jsonObject.getInt("success") == 1) {

                        String base64encodedPic = jsonObject.getString("data");
                        byte[] picbyes = Base64.decode(base64encodedPic, Base64.DEFAULT);
                        user.Picture = BitmapFactory.decodeByteArray(picbyes, 0, picbyes.length);
                        pic.setImageBitmap(user.Picture);
                    } else {

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

    void PopulateUserData() {

        daySteps.setText(Integer.toString(user.DaySteps()));
        coins.setText(Integer.toString(user.Coins()));
        level.setText("Level ?");

        GetUserPicture();

        int steps = user.DaySteps();
        int zero = MAX_STEPS - steps;
        stepChart.setOnTouchListener(null);
        stepChart.setDescription(null);
        stepChart.setDrawEntryLabels(false);
        stepChart.getLegend().setEnabled(false);

        List<PieEntry> entries = new ArrayList<>();
        entries.add(new PieEntry((float) steps, 0));
        entries.add(new PieEntry((float) zero, 1));

        PieDataSet dataSet = new PieDataSet(entries, "Steps");
        int[] colors = {ContextCompat.getColor(getContext(), R.color.bsbBlue)
                , ContextCompat.getColor(getContext(), R.color.lightGrey)};
        dataSet.setColors(colors);
        dataSet.setDrawValues(false);

        PieData data = new PieData(dataSet);
        stepChart.setData(data);
        stepChart.invalidate();

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
