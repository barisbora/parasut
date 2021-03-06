<?php namespace barisbora\Parasut\Methods;

use barisbora\Parasut\Dependencies\Model;

class SalesInvoice extends Eloquent
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {

        $data = $this->endpoint( 'sales_invoices/', 'get', $this->query->query() );

        return Model::build( $data );

    }

    public function find( $id )
    {

        $data = $this->endpoint( 'sales_invoices/' . $id, 'get', $this->query->query() );

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
