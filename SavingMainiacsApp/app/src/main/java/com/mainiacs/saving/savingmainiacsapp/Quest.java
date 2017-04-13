package com.mainiacs.saving.savingmainiacsapp;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by zax on 4/12/17.
 */

public class Quest implements Parcelable {

    protected Quest(Parcel in) {
    }

    public static final Creator<Quest> CREATOR = new Creator<Quest>() {
        @Override
        public Quest createFromParcel(Parcel in) {
            return new Quest(in);
        }

        @Override
        public Quest[] newArray(int size) {
            return new Quest[size];
        }
    };

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
    }
}
