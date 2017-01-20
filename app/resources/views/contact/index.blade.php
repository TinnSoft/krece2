
@extends('home')

   

@section('content')
  
    <style>
        .green {color: green;}
        .red {color: red;}
    </style>

 {!!Html::script('/js/vue-library/vue.min.js')!!}
  {!!Html::script('/js/libraries/axios.min.js')!!}
{!!Html::script('/js/functions/DataTable_initializer.js')!!}
{!!Html::style('/themes/krece/css/plugins/sweetalert/sweetalert.min.css')!!}  
  {!!Html::script('/themes/krece/js/plugins/sweetalert/sweetalert.min.js')!!}  

    <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Gestión de Contactos</h2>
                    <ol class="breadcrumb">
                        <li class="active">
                            <strong>Inicio</strong>      
                        </li>
                    </ol>
                </div>                
            </div>           

   <div  class="row  border-bottom white-bg dashboard-header">
        <div class="panel-body">
       
            @if( $contacts->count())  
              <div class="row">            
                <div class="col-lg-12 text-right">
                    <a href="{{route('contact.create')}}" class="btn btn-primary btn-sm pull-right"> 
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Contacto</a>                     
                </div>               
            </div>           
            <div class="hr-line-dashed"></div>      

            <div id="contact"  class="ibox-content">
               <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-data">
                   
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>NIT</th>
                        <th>Teléfono 1</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>     
                   
                    @foreach ($contacts as $cont)
                        <tr>                                                 
                                                
                            <td>{{$cont->name}}</td>
                            <td>{{$cont->nit}}</td>
                             <td>{{$cont->phone1}}</td>
                            <td>{{$cont->observation}}</td>
                            <td class="text-right">
                                <a href="{{route('contact.show', $cont)}}" >
                                    <span id="icon-detail" 
                                        title="Ver cotización" 
                                        class="glyphicon glyphicon-eye-open fa-1x green">
                                    </span>
                                </a>
                                <a href="{{route('contact.edit', $cont)}}" >
                                    <span id="icon-detail" 
                                        title="Editar cotización" 
                                        class="glyphicon glyphicon-pencil fa-1x green">
                                    </span>
                                </a>
                                <a>
                                    <span id="icon-detail"  
                                        @click="remove({{$cont->id}})" 
                                        title="Eliminar cotización" 
                                        class="glyphicon glyphicon-remove fa-1x red">
                                    </span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            
            @else
                  <table class="table table-striped table-hover dataTables-data">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>NIT</th>
                        <th>Teléfono 1</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                </table>
              
                <div class="alert alert-info" role="alert">
                     <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                         Aún no tienes contactos creados. Por que no empiezas
                        <a href="{{route('contact.create')}}"><strong>creando uno?</a> </strong>
                </div>                   
            @endif
        </div>       
      

    </div>


<script>
  @if(Session::has('message'))
     var type = "{{ Session::get('alert-type', 'info') }}";
    setTimeout(function() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
               
            switch(type){
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;

                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;

                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        }, 1300);


  @endif
</script>

<script>
var app = new Vue({
  el: '#contact',
   data()  {
    return {
    idsel:""
  }},
  methods: {
    remove:function(val) {
      let self = this;
      swal({
        title: "Estas seguro?",
        text: "Una vez eliminado este registro no se podrá recuperar",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
      }, function(isConfirm) {
        if (isConfirm) {
             var vm = this
            // axios.put('/taxes/' + vm.form.id, vm.form)
                axios.delete('/taxes/' + val)
                    .then(function(response) {
                        if(response.data.deleted) {
                            swal("Eliminado!", "El registro ha sido eliminado correctamente!!.", "success");
                            //vm.$router.push(vm.redirect)
                        }
                    })
                    .catch(function(error) {
                         swal("Error!", "El registro no se ha eliminado correctamente, intente de nuevo!!.", "error");
                        console.log(error)
                    })

                    swal("Eliminado!", "El registro ha sido eliminado correctamente!!.", "success");
          
        } else {
          swal("Cancelado", "Cancelado :)", "error");
        }
      }.bind(this)); 
    }
  }
})
</script>

@endsection




