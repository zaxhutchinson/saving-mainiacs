package com.mainiacs.saving.savingmainiacsapp;

import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.mainiacs.saving.savingmainiacsapp.UserQuestInfoFragment.OnListFragmentInteractionListener;

import java.util.List;

public class UserQuestInfoViewAdapater extends RecyclerView.Adapter<UserQuestInfoViewAdapater.ViewHolder> {

    private final List<UserQuestInfo> questList;
    private final OnListFragmentInteractionListener mListener;

    public UserQuestInfoViewAdapater(List<UserQuestInfo> items, OnListFragmentInteractionListener listener) {
        questList = items;
        mListener = listener;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.fragment_userquestinfo_item, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final ViewHolder holder, int position) {
        holder.questInfo = questList.get(position);
        holder.userQuestIdView.setText(Integer.toString(questList.get(position).getUserQuestId()));
        holder.questIdView.setText(Integer.toString(questList.get(position).getQuestId()));
        holder.charityIdView.setText(Integer.toString(questList.get(position).getCharityId()));
        holder.amountView.setText(Integer.toString(questList.get(position).getRewardAmount()));
        holder.dateView.setText(questList.get(position).getDate());

        holder.mView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (null != mListener) {
                    // Notify the active callbacks interface (the activity, if the
                    // fragment is attached to one) that an item has been selected.
                    mListener.onListFragmentInteraction(holder.questInfo);
                }
            }
        });
    }

    @Override
    public int getItemCount() {
        return questList.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {
        public final View mView;
        public final TextView userQuestIdView;
        public final TextView questIdView;
        public final TextView charityIdView;
        public final TextView amountView;
        public final TextView dateView;
        public UserQuestInfo questInfo;

        public ViewHolder(View view) {
            super(view);
            mView = view;
            userQuestIdView = (TextView) view.findViewById(R.id.user_quest_id);
            questIdView = (TextView) view.findViewById(R.id.quest_id);
            charityIdView = (TextView) view.findViewById(R.id.quest_charity_id);
            amountView = (TextView) view.findViewById(R.id.quest_amount);
            dateView = (TextView) view.findViewById(R.id.quest_date);
        }

        @Override
        public String toString() {
            return super.toString() + " '" + userQuestIdView.getText() + "'";
        }
    }
}
