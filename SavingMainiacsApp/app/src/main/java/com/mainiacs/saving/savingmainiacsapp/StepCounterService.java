package com.mainiacs.saving.savingmainiacsapp;

import android.app.IntentService;
import android.content.Intent;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.support.annotation.Nullable;

/**
 * Created by zax on 4/27/17.
 */

public class StepCounterService extends IntentService implements SensorEventListener {

    public StepCounterService() {
        super("StepCounterService");

    }

    @Override
    protected void onHandleIntent(@Nullable Intent intent) {

    }

    @Override
    public void onSensorChanged(SensorEvent event) {

    }

    @Override
    public void onAccuracyChanged(Sensor sensor, int accuracy) {

    }
}
