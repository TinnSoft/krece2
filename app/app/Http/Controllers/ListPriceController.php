<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\ListPrice;

class ListPriceController extends Controller
{

    public function index()
    {
           
        $listprice = ListPrice::with('listprice_type')
                ->where('account_id',  Auth::user()->account_id)
                ->where('isDeleted',  0)
                ->orderBy('created_at', 'asc')
                ->select('id', 'name','type_id','isDefault',
                'value','isEnabled'
                    )->get();    
            

        return view('list_price.index',compact('listprice'));
  
    }



   

    public function create()
    {
        return view('list_price.create');        
    }
        
    public function store(Request $request)
    {   
            

        $this->validate($request, [     
            'name' => 'required',              
            'type_id' => 'required'
        ]);

        $data = $request->except('bank_account');       

        $currentPublicId = ListPrice::where('account_id',  Auth::user()->account_id)->max('public_id')+1;
        $data['public_id'] = $currentPublicId;
        $data['account_id'] = Auth::user()->account_id;
        $data['user_id'] = Auth::user()->id;

         
         $account = ListPrice::create($data);
        
        return response()
            ->json([
                'created' => true,
                'id' => $account->id
            ]);
    }

  

    public function edit($id)
    {
        
        $listprice = ListPrice::where('account_id',  Auth::user()->account_id)
                        ->where('isDeleted',  0)
                     ->select('id', 'name','type_id','isDefault',
                        'value','isEnabled'
                            )
                    ->find($id);  
        
  
         if (!$listprice)
        {
            $notification = array(
               'message' => 'No se encontró ninguna referencia creada en nuestra base de datos!', 
                'alert-type' => 'error'
            );
          return redirect('/list_price')->with($notification);
        }
        $listprice['type_id']=(string) $listprice['type_id'];
         return view('list_price.edit', compact('listprice'));
         
         
    }

    public function update(Request $request, $id)
    {        
              
         $this->validate($request, [     
            'name' => 'required',              
            'type_id' => 'required'
        ]);
       
        $lprice = ListPrice::findOrFail($id);
        
       $data = $request->except('listprice_type'); 
        $data['user_id'] = Auth::user()->id;

        $lprice->update($data);

        return response()
            ->json([               
                'updated' => true,
                'id' => $lprice->id              
            ]);
    }
    
    public function destroy($id)
    {
         $lprice = ListPrice::findOrFail($id);

            $lprice['isDeleted']=1;
            $lprice['deleted_at']=$now = Carbon::now();
            $lprice->save();
            
            return response()
            ->json([
                'deleted' => true
            ]);
    }
}
