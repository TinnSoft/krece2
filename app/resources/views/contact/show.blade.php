@extends('home')

<!--<link href="/css/style_tables.css" rel="stylesheet">
-->
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-sm-4">
                <h2>Contacto No: {{$contact->public_id}}</h2>
                <ol class="breadcrumb">
                <li>
                    <a href="{{route('contact.index')}}">Inicio</a>
                </li>
                <li class="active">
                    <strong>Cotizaciones</strong>                            
                </li>
            </ol>
        </div>
                
    </div>           

          
            <div class="row wrapper wrapper-content">
            
                                       <div class="ibox-title">
                                       
                                            <p>
                                                <a href="{{route('contact.index')}}" class="btn btn-info btn-sm "> 
                                                <span class="glyphicon glyphicon-plus"></span>&nbsp;Crear factura de venta</a> 
                                                
                                                <a href="{{route('contact.index')}}" class="btn btn-info btn-sm "> 
                                                <span class="glyphicon glyphicon-plus"></span>&nbsp;Crear factura de compra</a> 

                                                <a href="{{route('contact.edit', $contact)}}" class="btn btn-info btn-sm "> 
                                                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Editar</a> 
                                                
                                                <a href="{{route('contact.create')}}" class="btn btn-primary btn-sm pull-right"> 
                                                <span class="glyphicon glyphicon-user"></span>&nbsp;Nuevo contacto</a> 

                                                
                                            </p>
                                     
                                        </div>
                            
                   
                 </div>
          
    


  <div class="row">
         
                <div class="wrapper wrapper-content">
                    <div class="ibox-content p-xl">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span><strong>Cotizado a: </strong>  <h4 class="text-navy">
                                      @if (count($contact->contact) > 0)
                                                 {{$contact->contact->name}}
                                        @endif</h4>
                                    </span><br/>

                                    <span><strong>Observaciones: </strong>  
                                      {{$contact->observations}}
                                    </span><br/>
                                    
                                    <span><strong>Vendedor: </strong>  
                                     {{$contact->seller_id}}
                                    </span><br/>
                                    
                                     <span><strong>Lista de precios: </strong>  
                                        @if (count($contact->list_price) > 0)
                                            {{$contact->list_price->name}}
                                        @endif
                                     </span><br/>
                                
                                      <span><strong>Moneda: </strong>  
                                        {{$contact->currency_code}}
                                    </span><br/>

                                </div>
                                    
                                <div class="col-sm-6 text-right">
                                    <h4>Cotización No.</h4>
                                    <h4 class="text-navy">{{$contact->public_id}}</h4><br/>                                    
                                    <p>
                                        <span><strong>Fecha:</strong>  {{$contact->date}} </span><br/>
                                        <span><strong>Vence en:</strong> {{$contact->due_date}}      </span>
                                    </p>
                                </div>
                            </div>

                           

                            
                        </div>
                </div>
         
        </div>


@endsection
