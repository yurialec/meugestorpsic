<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;

class Anamnese extends Model
{
    protected $table = 'anamnese';
    protected $guarded = [];

    protected $fillable = [
        'patient_id',
        'service_frequency'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function attitudeWithTheInterviewer()
    {
        return $this->hasOne(AnamneseAttitudeWithTheInterviewer::class);
    }

    public function awarenessOfCurrentIllness()
    {
        return $this->hasOne(AnamneseAwarenessOfCurrentIllness::class);
    }

    public function contents()
    {
        return $this->hasOne(AnamneseContent::class);
    }

    public function denialOfSelf()
    {
        return $this->hasOne(AnamneseDenialOfSelf::class);
    }

    public function diagnosticHypothesis()
    {
        return $this->hasOne(AnamneseDiagnosticHypothesis::class);
    }

    public function expansionOfTheSelf()
    {
        return $this->hasOne(AnamneseExpansionOfTheSelf::class);
    }

    public function familyHistory()
    {
        return $this->hasOne(AnamneseFamilyHistory::class);
    }

    public function guidance()
    {
        return $this->hasOne(AnamneseGuidance::class);
    }

    public function historyOfPresentIllness()
    {
        return $this->hasOne(AnamneseHistoryOfPresentIllness::class);
    }

    public function language()
    {
        return $this->hasOne(AnamneseLanguage::class);
    }

    public function mood()
    {
        return $this->hasOne(AnamneseMood::class);
    }

    public function notes()
    {
        return $this->hasOne(AnamneseNote::class);
    }

    public function personalHistory()
    {
        return $this->hasOne(AnamnesePersonalHistory::class);
    }

    public function psychicExam()
    {
        return $this->hasOne(AnamnesePsychicExam::class);
    }

    public function sensePerception()
    {
        return $this->hasOne(AnamneseSensePerception::class);
    }

    public function thought()
    {
        return $this->hasOne(AnamneseThought::class);
    }

    public function treatment()
    {
        return $this->hasOne(AnamneseTreatment::class);
    }
}