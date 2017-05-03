package com.mainiacs.saving.savingmainiacsapp;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.mainiacs.saving.savingmainiacsapp.UserQuestInfoFragment.OnListFragmentInteractionListener;

import java.util.List;

public class UserQuestInfoViewAdapter extends RecyclerView.Adapter<UserQuestInfoViewAdapter.ViewHolder> {

    private final List<UserQuestInfo> questList;
    private final OnListFragmentInteractionListener mListener;

    private final boolean showButtons;

    public UserQuestInfoViewAdapter(List<UserQuestInfo> items, boolean showButtonsFlag, OnListFragmentInteractionListener listener) {
        questList = items;
        mListener = listener;
        showButtons = showButtonsFlag;
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

        holder.questName.setText(questList.get(position).getQuestName());
        holder.questRewardAmount.setText(String.valueOf(questList.get(position).getRewardAmount()));
        holder.questDescription.setText(questList.get(position).getQuestDescription());
        holder.questCharityName.setText(questList.get(position).getCharityName());
        holder.questDate.setText(questList.get(position).getDate());
        
        holder.mView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                if (holder.questDetailsContainer.getVisibility() == View.VISIBLE) {
                    holder.questDetailsContainer.setVisibility(View.GONE);
                } else {
                    holder.questDetailsContainer.setVisibility(View.VISIBLE);
                }

                if (null != mListener) {
                    // Notify the active callbacks interface (the activity, if the
                    // fragment is attached to one) that an item has been selected.
                    mListener.onListFragmentInteraction(holder.questInfo);
                }
            }
        });

        if (showButtons) {

            holder.leaveQuestButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    // TODO: leave quest and refresh list
                }
            });

            holder.completeQuestButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    // TODO: mark quest as complete and refresh list
                }
            });

        } else {
            holder.leaveQuestButton.setVisibility(View.GONE);
            holder.completeQuestButton.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() {
        return questList.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {
        public final View mView;
        public final TextView questName;
        public final TextView questRewardAmount;
        public final LinearLayout questDetailsContainer;
        public final TextView questDescription;
        public final TextView questCharityName;
        public final TextView questDate;
        public final Button leaveQuestButton;
        public final Button completeQuestButton;
        public UserQuestInfo questInfo;

        public ViewHolder(View view) {
            super(view);
            mView = view;
            questName = (TextView) view.findViewById(R.id.user_quest_name);
            questRewardAmount = (TextView) view.findViewById(R.id.user_quest_reward);
            questDetailsContainer = (LinearLayout) view.findViewById(R.id.user_quest_details);
            questDescription = (TextView) view.findViewById(R.id.quest_description);
            questCharityName = (TextView) view.findViewById(R.id.quest_charity_name);
            questDate = (TextView) view.findViewById(R.id.quest_accepted_date);
            leaveQuestButton = (Button) view.findViewById(R.id.user_quest_delete);
            completeQuestButton = (Button) view.findViewById(R.id.user_quest_complete);
        }

        @Override
        public String toString() {
            return super.toString() + " '" + questName.getText() + "'";
        }
    }
}
