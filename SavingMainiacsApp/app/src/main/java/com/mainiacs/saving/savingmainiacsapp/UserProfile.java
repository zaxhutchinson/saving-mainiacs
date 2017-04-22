package com.mainiacs.saving.savingmainiacsapp;

/**
 * Created by zax on 4/21/17.
 */

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class UserProfile implements Parcelable {

    private String user_name;
    private String login_name;
    private String email_address;
    private int day_steps;
    private int month_steps;
    private int total_steps;
    private double last_lat;
    private double last_long;
    private int coins;
    private int total_coins;

    public UserProfile() {

    }

    public UserProfile(JSONObject obj) {
        try {
            JSONArray jarr = obj.optJSONArray("results");
            JSONObject data = jarr.getJSONObject(0);
            user_name = data.getString("UserName");
            login_name = data.getString("LoginName");
            email_address = data.getString("EmailAddress");
            day_steps = data.getInt("DaySteps");
            month_steps = data.getInt("MonthSteps");
            total_steps = data.getInt("TotalSteps");
            last_lat = data.getDouble("LastLatitude");
            last_long = data.getDouble("LastLongitude");
            coins = data.getInt("Coins");
            total_coins = data.getInt("TotalCoins");
        }
        catch(JSONException e) {
            e.printStackTrace();
        }
    }

    protected UserProfile(Parcel in) {
        user_name = in.readString();
        login_name = in.readString();
        email_address = in.readString();
        day_steps = in.readInt();
        month_steps = in.readInt();
        total_steps = in.readInt();
        last_lat = in.readDouble();
        last_long = in.readDouble();
        coins = in.readInt();
        total_coins = in.readInt();
    }

    public static final Creator<UserProfile> CREATOR = new Creator<UserProfile>() {
        @Override
        public UserProfile createFromParcel(Parcel in) {
            return new UserProfile(in);
        }

        @Override
        public UserProfile[] newArray(int size) {
            return new UserProfile[size];
        }
    };

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeString(user_name);
        dest.writeString(login_name);
        dest.writeString(email_address);
        dest.writeInt(day_steps);
        dest.writeInt(month_steps);
        dest.writeInt(total_steps);
        dest.writeDouble(last_lat);
        dest.writeDouble(last_long);
        dest.writeInt(coins);
        dest.writeInt(total_coins);
    }
}
