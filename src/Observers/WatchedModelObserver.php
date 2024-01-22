<?php

namespace Canzell\Aws\Sns\Observers;

use Canzell\Aws\Sns\Models\AbstractWatchedModel;
use Canzell\Facades\SNS;
use Illuminate\Support\Facades\Auth;

class WatchedModelObserver
{

    public function created(AbstractWatchedModel $model)
    {
        $requester = Auth::user();
        $details = [
            'id' => $model->getKey(),
            'requester_id' => $requester->id ?? null,
            'data' => $model->getSnsMessageData()
        ];
        
        $this->publish($model, 'created', $details);
    }
    
    public function updated(AbstractWatchedModel $model)
    {
        $requester = Auth::user();
        $details = [
            'id' => $model->getKey(),
            'requester_id' => $requester->id ?? null,
            'changes' => $model->getChanges(),
            'data' => $model->getSnsMessageData(),
        ];
        
        $this->publish($model, 'updated', $details);
    }
    
    public function deleted(AbstractWatchedModel $model)
    {
        $requester = Auth::user();
        $details = [
            'id' => $model->getKey(),
            'requester_id' => $requester->id ?? null,
            'data' => $model->getSnsMessageData(),
        ];
        
        $this->publish($model, 'deleted', $details);
    }
    
    public function restored(AbstractWatchedModel $model)
    {
        $requester = Auth::user();
        $details = [
            'id' => $model->getKey(),
            'requester_id' => $requester->id ?? null,
            'data' => $model->getSnsMessageData(),
        ];
        
        $this->publish($model, 'restored', $details);
    }
    
    public function forceDeleted(AbstractWatchedModel $model)
    {
        $requester = Auth::user();
        $details = [
            'id' => $model->getKey(),
            'requester_id' => $requester->id ?? null,
            'data' => $model->getSnsMessageData(),
        ];
        
        $this->publish($model, 'force_deleted', $details);
    }

    private function publish(AbstractWatchedModel $model, string $event, object|array $details)
    {
        Sns::publish($model->getSnsTopicArn(), "{$model->getSnsPrefix()}_{$event}", $details);
    }

}
