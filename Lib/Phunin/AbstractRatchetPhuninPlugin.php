<?php

class AbstractRatchetPhuninPlugin {
    
    protected function resultDecorator(\React\Promise\DeferredResolver $deferredResolver) {
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($result) use($deferredResolver) {
            $values = new \SplObjectStorage;

            foreach ($result as $key => $value) {
                $values->attach(new \PhuninNode\Value($key, $value));
            }

            $deferredResolver->resolve($values);
        });
        return $deferred->resolver();
    }
    
}