<?php
namespace Canzell\Aws\Sns\Models;

use Canzell\Aws\Sns\Observers\WatchedModelObserver;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractWatchedModel extends Model
{

    protected static function booted()
    {
        self::observe(WatchedModelObserver::class);
    }

    public function getSnsTopicArn(): string
    {
        $class = $this::class;
        return $this->snsTopicArn ?? config("aws.sns.watch_entities.{$class}");
    }
    
    public function getSnsPrefix(): string
    {
        if ($this->snsPrefix == null) {
            $classParts = explode('\\', strtolower($this::class));
            $prefix = array_pop($classParts);
            return $prefix;
        } else return $this->snsPrefix;
    }

    public function getSnsMessageData(): array
    {
        return $this->toArray();
    }

}