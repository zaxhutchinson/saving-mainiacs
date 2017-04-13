package com.mainiacs.saving.savingmainiacsapp;


import android.os.Parcel;
import android.os.Parcelable;

import java.util.List;
import java.util.LinkedList;

/**
 * Created by zax on 4/12/17.
 */

public class Charity implements Parcelable{

    private int id;
    private String name;
    private String address;
    private double latitude;
    private double longitude;
    private List<Quest> quests;

    public Charity(int id, String name, String address, double latitude, double longitude) {
        this.id = id;
        this.name = name;
        this.address = address;
        this.longitude = longitude;
        this.latitude = latitude;

        quests = new LinkedList<Quest>();
    }

    private Charity(Parcel in) {
        id = in.readInt();
        name = in.readString();
        address = in.readString();
        latitude = in.readDouble();
        longitude = in.readDouble();
        quests = in.createTypedArrayList(Quest.CREATOR);
    }

    public static final Creator<Charity> CREATOR = new Creator<Charity>() {
        @Override
        public Charity createFromParcel(Parcel in) {
            return new Charity(in);
        }

        @Override
        public Charity[] newArray(int size) {
            return new Charity[size];
        }
    };

    public int ID() {
        return id;
    }
    public String Name() {
        return name;
    }
    public String Address() {
        return address;
    }
    public double Latitude() {
        return latitude;
    }
    public double Longitude() {
        return longitude;
    }
    public void Name(String name) {
        this.name = name;
    }
    public void Address(String address) {
        this.address = address;
    }
    public void Latitude(double latitude) {
        this.latitude = latitude;
    }
    public void Longitude(double longitude) {
        this.longitude = longitude;
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeString(name);
        dest.writeString(address);
        dest.writeDouble(latitude);
        dest.writeDouble(longitude);
        dest.writeTypedList(quests);
    }
}
