<?php namespace barisbora\Parasut\Methods;

use barisbora\Parasut\Parameters\SalesInvoiceParameter;

class SalesInvoice extends Model
{

    /**
     * @param callable|null $closure
     * @return string
     */
    public function get( callable $closure = null )
    {

        $parameters = new SalesInvoiceParameter();

        if ( $closure ) $closure( $parameters );

        $data = $this->endpoint( 'sales_invoices/9397345', 'get', $parameters->query() );
        //$data = $this->endpoint( 'sales_invoices', 'get', $parameters->query() );

        dd( self::make( $data, self::class) );

        dd($data);

        return $data;

    }

}
