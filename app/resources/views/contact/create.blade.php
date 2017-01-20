@extends('home')

@section('content')


 <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2 >Nuevo contacto</h2>
                    <ol class="breadcrumb">                       
                        <li>
                            <a href="{{route('contact.index')}}">Inicio</a>
                        </li>
                         <li class="active">
                            <strong>Crear</strong>
                        </li>
                    </ol>
                </div>
                
            </div>           

 <div class="row  border-bottom white-bg dashboard-header">
    <div id="contact">   
        <div class="panel" v-cloak>           
            <div class="panel-body">
                @include('contact.form')
            </div>
            <div class="panel-footer">
                <div class="clearfix">                 
                    <button class="btn btn-primary pull-right" @click="create" :disabled="isProcessing"><i class="fa fa-save"></i>&nbsp;Crear</button>                   
                    <a href="{{route('contact.index')}}" class="btn btn-default pull-right">&nbsp;Cancelar</a>
                 </div>
            </div>
        </div>
    </div>
   
</div>
@endsection


@push('scripts')
 
    {!!Html::script('/js/libraries/axios.min.js')!!}
    {!!Html::script('/js/libraries/moment.min.js')!!}
    {!!Html::script('/themes/krece/js/plugins/datapicker/bootstrap-datepicker.js')!!}    
    {!!Html::style('/themes/krece/css/plugins/datapicker/datepicker3.css')!!}
    {!!Html::script('/js/components/datepicker/datepicker-locale.es.min.js')!!}
    {!!Html::script('/js/vue-library/vue.min.js')!!}
    {!!Html::script('/js/components/vue-multiselect/multiselect.min.js')!!}
    {!!Html::script('/js/libraries/accounting.js')!!}
    <script type="text/javascript">
        //Vue.http.headers.common['X-CSRF-TOKEN'] = '{{csrf_token()}}';
        Vue.prototype.$http = axios;
        window._form = {
            name:'',
            nit:'',
            address:'',
            city:'',
            email:'',
            phone1:'',
            phone2:'',
            fax:'',
            phone_mobile:'',
            list_price_id:'',
            list_price:null,
            seller_id:'',
            seller:null,
            payment_terms_id:'',
            paymentTerms:null,
            observation:'',
            include_account_state:'',
            isProvider:'',
            isCustomer:'',
             contactdetail: [{
                name: '',
                last_name:null,
                email:'',
                phone: '',
                phone_mobile: '',
                notify: false
           }]
        };

    </script>
   
       
   {!!Html::script('/js/components/datepicker/datepicker-vue.js')!!} 
    {!!Html::script('/js/functions/contact.js')!!}

      

   
@endpush
