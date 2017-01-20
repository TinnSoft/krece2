Vue.config.devtools = true;
Vue.config.debug = true;

var app = new Vue({
  el: '#estimate',
  components: {
        "Multiselect": VueMultiselect.Multiselect
    },
  data()  {
    return {
    isProcessing: false,
    name: '',
    form: {},
    errors: {},
    customer_list:[],
    vendors:[],
    currency:[],
    listPrice:[],   
    product_list:[],
    taxes:[{text:"(0%) - Ninguno",value:0},{text:"(19%) - IVA",value:19},{text:"(16%) - IVA",value:16}]
  }},
    //similar on load event

  created: function () {  
      this.fetchData();
      Vue.set(this.$data, 'form', _form); 
    },
  beforeMount() {
             this.getCurrentDate();
        },

  methods: {  
      onInputContact:function(val)
      {
        if(val)
        {this.form.customer_id=val.id; }
        else
        {this.form.customer_id=''; }      
      },
       onInputSeller:function(val)
      {
        if(val)
        {this.form.seller_id=val.id; }
        else
        {this.form.seller_id=''; }      
      },
      onInputTax:function(val)
      {
        if (val.tax_value)
        {
          val.tax_amount=val.tax_value.value;
        }
        else{ 
          val.tax_amount="";
          }  
      },
      onInputlistprice:function(val)
      {
        if(val)
        {this.form.list_price_id=val.id; }
        else
        {this.form.list_price_id=''; }      
      },
      onInputCurrency:function(val)
      {
        if(val)
        {this.form.currency_code=val.code; }
        else
        {this.form.currency_code=''; }      
      },
      onInputProduct:function(val)
      {
        if (val.product)
        {
          val.description=val.product.description;
          val.unit_price=val.product.sale_price;
          val.product_id=val.product.id;
        }
        else{ 
          val.product_id="";
          }
      },
      currencyLabel({
        code,
        symbol
        }) {      
        if(code)
        {
          if(!symbol)
          {
            symbol="$";
          }
        return `${code} — [${symbol}]`
        }
      },
      
      getCurrentDate: function()
      {
        var d = new Date();      
        var n = d.toLocaleDateString();
        if (this.form.date=="")
        {
          this.form.date=n;
        }
      },
      addLine: function(e) {      
        this.form.estimatedetail.push({ product_id: null,
                  name:'',
                  description:'',
                  unit_price: 0,
                  discount: 0,
                  quantity: 1,
                  tax_amount: 0});           
      },
      removeItem: function(detail) {      
        var index = this.form.estimatedetail.indexOf(detail)
        this.form.estimatedetail.splice(index,1);
      },
      fetchData: function()
      {    
        //carga de los datos del header
        var vm = this
                  axios.get('/getEstimateBaseInfo')
                      .then(function(response) {                          
                          Vue.set(vm.$data, 'customer_list', response.data.contacts);
                          Vue.set(vm.$data, 'currency', response.data.currency);
                          Vue.set(vm.$data, 'listPrice', response.data.listprice);
                          Vue.set(vm.$data, 'vendors', response.data.sellers);
                          Vue.set(vm.$data, 'product_list', response.data.productlist);

                          if (vm.$data.form.public_id=="")
                          {                          
                            vm.$data.form.public_id=response.data.public_id;
                          }
                      })
                      .catch(function(error) {  
                          Vue.set(vm.$data, 'errors', error);
                      })                  
      },
      create: function() {      
          var vm = this;
          vm.isProcessing = true;
          axios.post('/estimate', vm.form)
            .then(function (response) {
              if(response.data.created) {          
                window.location = '/estimate/' + response.data.id;
              } else {
                vm.isProcessing = false;
              }
            })
            .catch(function (error) {        
              vm.isProcessing = false;
              Vue.set(vm.$data, 'errors', error.response.data);
            });
      },

      update: function() {
      var vm = this;    
        vm.isProcessing = true;
        axios.put('/estimate/' + vm.form.id, vm.form)
          .then(function(response) {
            if(response.data.updated) {
              window.location = '/estimate/' + response.data.id;;
            } else {             
              vm.isProcessing = false;
            }
          })
          .catch(function(error) {
            vm.isProcessing = false;
            Vue.set(vm.$data, 'errors', error.response.data);
          })
      }   
    },  
 //valores calculados
  computed: {
    subTotal: function() {
      var _subtotal= this.form.estimatedetail.reduce(function(carry, estimatedetail) {
        return carry + (parseFloat(estimatedetail.quantity) * parseFloat(estimatedetail.unit_price));
      }, 0);
       this.form.sub_total=_subtotal;
      return _subtotal;
    },
     DiscountsTotal: function() {
      var discountsTot= this.form.estimatedetail.reduce(function(carry, estimatedetail) {
        return carry + (((parseFloat(estimatedetail.quantity) * parseFloat(estimatedetail.unit_price))* parseFloat(estimatedetail.discount)))/100;        
      }, 0);      
      this.form.amount_discounts=discountsTot;
      return isNaN(discountsTot) ? 0:discountsTot
    },

      TaxesTotal: function() {
      var TaxTot= this.form.estimatedetail.reduce(function(carry, estimatedetail) {
       return carry + ((((parseFloat(estimatedetail.quantity) * parseFloat(estimatedetail.unit_price))
       -((parseFloat(estimatedetail.quantity) * parseFloat(estimatedetail.unit_price))* parseFloat(estimatedetail.discount))/100)* 
       parseFloat(estimatedetail.tax_amount)))/100; 
      }, 0);
      
      this.form.amount_taxes=TaxTot;
      
      return isNaN(TaxTot) ? 0:TaxTot
    },

    grandTotal: function() {
      var totalval= (isNaN(this.subTotal)?0:parseFloat(this.subTotal)) - 
     (isNaN(this.DiscountsTotal)?0:parseFloat(this.DiscountsTotal))+(isNaN(this.TaxesTotal)?0:parseFloat(this.TaxesTotal));    
   

     this.form.total=totalval;
     return isNaN(totalval) ? 0:totalval
    }    
    }
})


/*Formato de moneda */
Vue.filter('formatCurrency', function (value) {
   return accounting.formatMoney(value);
});
