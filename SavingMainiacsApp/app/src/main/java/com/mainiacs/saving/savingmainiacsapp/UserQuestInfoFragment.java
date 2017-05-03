package com.mainiacs.saving.savingmainiacsapp;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import java.util.ArrayList;

public class UserQuestInfoFragment extends Fragment {

    private static final String ARG_QUESTS = "userQuests";
    private static final String ARG_QUEST_STATUS = "questStatus";

    private ArrayList<UserQuestInfo> questList;
    private int questStatus;
    private OnListFragmentInteractionListener mListener;

    public UserQuestInfoFragment() {
    }

    public static UserQuestInfoFragment newInstance(ArrayList<UserQuestInfo> quests, int questStatus) {
        UserQuestInfoFragment fragment = new UserQuestInfoFragment();
        Bundle args = new Bundle();
        args.putParcelableArrayList(ARG_QUESTS, quests);
        args.putInt(ARG_QUEST_STATUS, questStatus);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getArguments() != null) {
            questList = getArguments().getParcelableArrayList(ARG_QUESTS);
            questStatus = getArguments().getInt(ARG_QUEST_STATUS);
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_userquestinfo_list, container, false);

        // Set the adapter
        if (view instanceof RecyclerView) {
            RecyclerView recyclerView = (RecyclerView) view;

            // Only show the buttons to leave quest or mark as complete in active quest page
            boolean showButtons = questStatus == QuestFragment.QUEST_STATUS_ACTIVE;
            recyclerView.setAdapter(new UserQuestInfoViewAdapter(questList, showButtons, mListener));
        }
        return view;
    }


    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnListFragmentInteractionListener) {
            mListener = (OnListFragmentInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnListFragmentInteractionListener");
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
     * <p/>
     * See the Android Training lesson <a href=
     * "http://developer.android.com/training/basics/fragments/communicating.html"
     * >Communicating with Other Fragments</a> for more information.
     */
    public interface OnListFragmentInteractionListener {
        // TODO: Update argument type and name
        void onListFragmentInteraction(UserQuestInfo info);
    }
}
