<?php namespace barisbora\Parasut\Methods;

use barisbora\Parasut\Dependencies\Model;
use barisbora\Parasut\Parameters\ProductParameter;

class Product extends Eloquent
{

    /**
     * @param callable|null $closure
     * @return \Illuminate\Support\Collection
     */
    public function get( callable $closure = null )
    {

        $parameters = new ProductParameter();

        if ( $closure ) $closure( $parameters );

        $data = $this->endpoint( 'products', 'get', $parameters->query() );

        return Model::build( $data );

    }

    public function create( array $attributes )
    {

        $data = $this->endpoint( 'sales_invoices', 'post', [
            'json' => $attributes
        ] );

        dd($data);

        return Model::build( $data );

    }
}
