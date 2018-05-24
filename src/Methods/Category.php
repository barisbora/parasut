<?php namespace barisbora\Parasut\Methods;

use barisbora\Parasut\Dependencies\Model;

class Category extends Eloquent
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {

        $data = $this->endpoint( 'item_categories', 'get', $this->query->query() );

        return Model::build( $data );

    }

    /**
     * @param $id
     * @return Model
     */
    public function find( $id )
    {

        $data = $this->endpoint( 'item_categories/' . $id, 'get', $this->query->query() );

        return Model::build( $data );

    }

    public function create( array $attributes )
    {

        $data = $this->endpoint( 'sales_invoices', 'post', [
            'json' => $attributes,
        ] );

        dd( $data );

        return Model::build( $data );

    }
}
