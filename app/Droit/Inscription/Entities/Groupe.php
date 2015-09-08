<?php

namespace App\Droit\Inscription\Entities;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{

    protected $table = 'colloque_inscriptions_groupes';

    public $timestamps = false;

    protected $fillable = ['colloque_id', 'user_id', 'description', 'adresse_id'];

    public function getDocumentsAttribute()
    {
        $docs = [];

        $this->load('user','colloque','inscriptions');

        if(!empty($this->colloque->annexe))
        {
            $annexes = $this->colloque->annexe;

            if(in_array('bon',$annexes))
            {
                $inscriptions = $this->inscriptions;

                foreach($inscriptions as $inscription)
                {
                    $inscription->load('participant');
                    $bons = $this->getFile('bon',$this->colloque->id,$this->user->id,$inscription->participant->id);
                    $docs['groupe'][] = $bons;
                }

                unset($annexes['bon']);
            }

            foreach($this->colloque->annexe as $id => $annexe)
            {
                $new =  $this->getFile($annexe,$this->colloque->id,$this->user->id);
                $docs = array_merge($new,$docs);
            }
        }

        return $docs;
    }

    public function getFile($annexe,$colloque_id,$user_id,$part = null)
    {
        $docs = [];
        $id   = $part;
        $part = ($part ? '-'.$part : '');
        $path = config('documents.colloque.'.$annexe.'');
        $file = public_path().$path.$annexe.'_'.$colloque_id.'-'.$user_id.$part.'.pdf';

        if (\File::exists($file))
        {
            $name = $annexe.'_'.$colloque_id.'-'.$user_id.$part.'.pdf';

            if($part)
            {
                $docs = ['file' => $file,'name' => $name];
            }
            else
            {
                $docs[$annexe]['file'] = $file;
                $docs[$annexe]['name'] = $name;
            }
        }

        return $docs;
    }

    public function user()
    {
        return $this->belongsTo('App\Droit\User\Entities\User');
    }

    public function colloque()
    {
        return $this->belongsTo('App\Droit\Colloque\Entities\Colloque');
    }

    public function inscriptions()
    {
        return $this->hasMany('App\Droit\Inscription\Entities\Inscription', 'group_id');
    }
}
