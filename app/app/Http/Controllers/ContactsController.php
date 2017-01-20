<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Contact_others;
use App\Models\PaymentTerms;
use App\Models\Seller;
use App\Models\ListPrice;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{

    public function index()
    {
        //Obtener las cotizaciones creadas hasta la fecha       
        $contacts = Contact::where('account_id',  Auth::user()->account_id)
               ->orderBy('created_at', 'desc')
               ->select( 'name','nit','address','account_id','user_id','public_id','city','email','phone1','phone2','fax',
                    'phone_mobile','list_price_id','seller_id','payment_terms_id','observation','include_account_state',
                    'isProvider','isCustomer'
               )->get();    

        return view('contact.index',compact('contacts'));
  
    }

    //Rtorna la información necesaria para el header de las facturas/cotizaciones.etc
    public function BaseInfo()
    {
    
        $paymentTerms = PaymentTerms::select('id', 'name')
                ->where('account_id',  Auth::user()->account_id)
                 ->where('isDeleted',  0)
               ->orderBy('created_at', 'asc')
               ->get();
        
        $sellers = Seller::select('id', 'name')
                ->where('account_id',  Auth::user()->account_id)
                ->where('isDeleted',  0)
                ->where('isEnabled',  1)
               ->orderBy('created_at', 'asc')
               ->get();

        $listPrice = ListPrice::select('id', 'name')
                ->where('account_id',  Auth::user()->account_id)
                ->where('isDeleted',  0)
                ->where('isEnabled',  1)
               ->orderBy('created_at', 'asc')
               ->get();
        
        $baseInfo=[                
                'paymentterms' => $paymentTerms,
               'sellers'=>$sellers,
               'listprice'=>$listPrice              
            ];
             
     return response()->json($baseInfo);

    }

    public function create()
    {
        return view('contact.create');        
    }
        
    public function store(Request $request)
    {   
        $this->validate($request, [     
            'name' => 'required'
        ]);

         
        
       
        $items=[];
        
        foreach($request->contactdetail as $item) {
              if($item['name'])
              {
                  if ( $item['notify']==true)
                  {
                        $item['notify']=1;
                  }
                  if ( $item['notify']==false)
                  {
                       $item['notify']=0;
                  }
                $item['account_id'] = Auth::user()->account_id; 
                $item['user_id'] = Auth::user()->id;      
                $items[] = new Contact_others($item);
              }
        }
        
      
        $data = $request->except('contactdetail');  

        $currentPublicId = Contact::where('account_id',  Auth::user()->account_id)->max('public_id')+1;
        $data['public_id'] = $currentPublicId;
        $data['account_id'] = Auth::user()->account_id;
        $data['user_id'] = Auth::user()->id;
        $data['isProvider'] =empty($data['isProvider']) ? 0 : $data['isProvider']; 
        $data['isCustomer'] =empty($data['isCustomer']) ? 0 : $data['isCustomer'];  
        $data['list_price_id'] =empty($data['list_price_id']) ? null : $data['list_price_id'];   
        $data['seller_id'] =empty($data['seller_id']) ? null : $data['seller_id']; 
        $data['payment_terms_id'] =empty($data['payment_terms_id']) ? null : $data['payment_terms_id']; 

        if ($data['isProvider']==true)
        {
            $data['isProvider']=1;
        }
         if ($data['isCustomer']==true)
        {
            $data['isCustomer']=1;
        }
        
       $contact = Contact::create($data);
        $contact->contact_others()->saveMany($items);

     
        return response()
            ->json([
                'created' => true,
                'id' => $contact->id
            ]);
    }

    public function show($id)
    {
        $contact = Contact::with('contact_others')->where('account_id',  Auth::user()->account_id)->find($id);
         
        
        if (!$contact)
        {
            $notification = array(
                'message' => 'No se encontró ninguna referencia de cotizacion creadas!', 
                'alert-type' => 'error'
            );

          return redirect('/contact')->with($notification);
        }
      
        return view('contact.show', compact('contact'));
    }

    //allows to export a date value to the customized format of the view 
    public static function setCustomDateFormat($dateval)
    {            
        $day=$dateval->day;
        $month=$dateval->month;
        $year=$dateval->year;
        return $day.'/'. $month.'/'.$year;   
    }

    public function edit($id)
    {
        
        $contact = contact::with('contactdetail')
        ->with('contact')
        ->with('list_price')
        ->with('currency')
        ->where('account_id',  Auth::user()->account_id)->find($id);        


         if (!$contact)
        {
            $notification = array(
                'message' => 'No se encontró ninguna referencia de cotizacion creadas!', 
                'alert-type' => 'error'
            );
          return redirect('/contact')->with($notification);
        }

        $contact['date']= $this->setCustomDateFormat(Carbon::parse($contact['date']));
        $contact['due_date']= $this->setCustomDateFormat(Carbon::parse($contact['due_date']));
        
         return view('contact.edit', compact('contact'));
         
         
    }

    public function update(Request $request, $id)
    {        
              
        $this->validate($request, [     
            'customer_id' => 'required',               
            'date' => 'required',
            'due_date' => 'required',
            'notes' => 'required',            
            'contactdetail.*.unit_price' => 'required|numeric|min:1',
            'contactdetail.*.quantity' => 'required|integer|min:1',
            'contactdetail.*.product_id' => 'required',    
        ]);
       
        $contact = contact::findOrFail($id);

        $products = collect($request->contactdetail)->transform(function($contactdetail) {
        $contactdetail['total'] = $contactdetail['quantity'] * $contactdetail['unit_price'];
        $contactdetail['user_id'] =  Auth::user()->id;
            return new contactDetail($contactdetail);
        });
        
        if($products->isEmpty()) {
            return response()
            ->json([
                'products_empty' => ['Uno o mas productos son requeridos.']
            ], 422);
        };
        
       $data = $request->except('contactdetail');       

        $data['user_id'] = Auth::user()->id;
        $data['seller_id'] =empty($data['seller_id']) ? null : $data['seller_id']; 
        $data['list_price_id'] =empty($data['list_price_id']) ? null : $data['list_price_id'];    
        $data['date']=Carbon::createFromFormat('d/m/Y', $data['date']);
        $data['due_date']= Carbon::createFromFormat('d/m/Y', $data['due_date']);
        $contact->update($data);
       
        contactDetail::where('contact_id', $contact->id)->delete();
        $contact->contactdetail()->saveMany($products);

        return response()
            ->json([
                'updated' => true,
                'id' => $contact->id              
            ]);
    }
    
    public function destroy($id)
    {
         return response()
            ->json([
                'products_empty' => ['Uno o mas productos son requeridos.']
            ], 422);
        /*
        $contact = contact::findOrFail($id);
        contactDetail::where('id', $contact->id)
            ->delete();
        $contact->delete();
        return redirect()
            ->route('contact.index');
            */
    }
}
