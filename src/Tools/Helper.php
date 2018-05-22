<?php namespace barisbora\Parasut\Tools;

trait Helper {

    /**
     * @param $response
     * @return mixed
     */
    protected function parseResponse( $response )
    {
        return json_decode( $response->getBody()->getContents() );
    }

}
