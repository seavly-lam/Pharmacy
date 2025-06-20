<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use QCod\AppSettings\Setting\AppSettings;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'products';
        if ($request->ajax()) {
            $products = Product::latest();
            return DataTables::of($products)
                ->addColumn('product',function($product){
                    $image = '';
                    if(!empty($product->purchase)){
                        $image = null;
                        if(!empty($product->purchase->image)){
                            $image = '<span class="avatar avatar-sm mr-2">
                            <img class="avatar-img" src="'.asset("storage/purchases/".$product->purchase->image).'" alt="image">
                            </span>';
                        }
                        return $product->purchase->product. ' ' . $image;
                    }                 
                })
                
                ->addColumn('category',function($product){
                    $category = null;
                    if(!empty($product->purchase->category)){
                        $category = $product->purchase->category->name;
                    }
                    return $category;
                })
                ->addColumn('price',function($product){                   
                    return settings('app_currency','$').' '. $product->price;
                })
                ->addColumn('quantity',function($product){
                    if(!empty($product->purchase)){
                        return $product->purchase->quantity;
                    }
                })
                ->addColumn('expiry_date',function($product){
                    if(!empty($product->purchase)){
                        return date_format(date_create($product->purchase->expiry_date),'d M, Y');
                    }
                })
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("products.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('products.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-product')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                        $deletebtn = '';
                    }
                    $btn = $editbtn.' '.$deletebtn;
                    return $btn;
                })
                ->rawColumns(['product','action'])
                ->make(true);
        }
        return view('admin.products.index',compact(
            'title'
        ));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'add product';
        $purchases = Purchase::get();
        return view('admin.products.create',compact(
            'title','purchases'
        ));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'product'=>'required|max:200',
            'price'=>'required|min:1',
            'discount'=>'nullable',
            'description'=>'nullable|max:255',
        ]);
        $price = $request->price;
        if($request->discount >0){
           $price = $request->discount * $request->price;
        }
        Product::create([
            'purchase_id'=>$request->product,
            'price'=>$price,
            'discount'=>$request->discount,
            'description'=>$request->description,
        ]);
        $notification = notify("Product has been added");
        return redirect()->route('products.index')->with($notification);
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $title = 'edit product';
        $purchases = Purchase::get();
        return view('admin.products.edit',compact(
            'title','product','purchases'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate($request,[
            'product'=>'required|max:200',
            'price'=>'required',
            'discount'=>'nullable',
            'description'=>'nullable|max:255',
        ]);
        
        $price = $request->price;
        if($request->discount >0){
           $price = $request->discount * $request->price;
        }
       $product->update([
            'purchase_id'=>$request->product,
            'price'=>$price,
            'discount'=>$request->discount,
            'description'=>$request->description,
        ]);
        $notification = notify('product has been updated');
        return redirect()->route('products.index')->with($notification);
    }

     /**
     * Display a listing of expired resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
   public function expired(Request $request)
{
    $title = "Expired / Expiring Soon Products";

    if ($request->ajax()) {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(30);

        // Get purchases expired or expiring within 30 days
        $purchases = Purchase::with('category')
            ->whereDate('expiry_date', '<=', $limitDate)
            ->get();

        return DataTables::of($purchases)
            ->addColumn('product', function ($purchase) {
                $image = '';
                if (!empty($purchase->image)) {
                    $image = '<span class="avatar avatar-sm mr-2">
                        <img class="avatar-img" src="' . asset("storage/purchases/" . $purchase->image) . '" alt="image">
                    </span>';
                }
                return $purchase->product . ' ' . $image;
            })

            ->addColumn('category', function ($purchase) {
                return $purchase->category ? $purchase->category->name : '';
            })

            ->addColumn('price', function ($purchase) {
                $product = Product::where('purchase_id', $purchase->id)->first();
                return $product ? settings('app_currency', '$') . ' ' . $product->price : '-';
            })

            ->addColumn('quantity', function ($purchase) {
                return $purchase->quantity ?? 0;
            })

            ->addColumn('discount', function ($purchase) {
                $product = Product::where('purchase_id', $purchase->id)->first();
                return $product ? ($product->discount ?? '0') . '%' : '0%';
            })

            ->addColumn('expiry_date', function ($purchase) use ($today) {
                $expiry = Carbon::parse($purchase->expiry_date);
                $daysLeft = $today->diffInDays($expiry, false); // negative if expired

                if ($daysLeft < 0) {
                    $label = '<span class="badge bg-danger">Expired</span>';
                } elseif ($daysLeft === 0) {
                    $label = '<span class="badge bg-danger">Expires Today</span>';
                } else {
                    $label = "<span class='badge bg-warning text-dark'>Expires in $daysLeft day" . ($daysLeft > 1 ? 's' : '') . "</span>";
                }

                return $expiry->format('d M, Y') . ' ' . $label;
            })

            ->addColumn('action', function ($purchase) {
                $product = Product::where('purchase_id', $purchase->id)->first();
                if (!$product) return '';

                $editbtn = '<a href="' . route("products.edit", $product->id) . '" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                $deletebtn = '<a data-id="' . $product->id . '" data-route="' . route('products.destroy', $product->id) . '" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';

                if (!auth()->user()->hasPermissionTo('edit-product')) {
                    $editbtn = '';
                }
                if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                    $deletebtn = '';
                }

                return $editbtn . ' ' . $deletebtn;
            })

            ->rawColumns(['product', 'expiry_date', 'action'])
            ->make(true);
    }

    return view('admin.products.expired', compact('title'));
}


    /**
     * Display a listing of out of stock resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function outstock(Request $request)
{
    $title = "Out of Stock Products";

    if ($request->ajax()) {
        $products = Product::with('purchase.category')
            ->whereHas('purchase', function ($query) {
                $query->where('quantity', '<=', 5); // Include 0 to 5
            })
            ->get();

        return DataTables::of($products)
            ->addColumn('product', function ($product) {
                $image = '';
                if (!empty($product->purchase)) {
                    if (!empty($product->purchase->image)) {
                        $image = '<span class="avatar avatar-sm mr-2">
                            <img class="avatar-img" src="' . asset("storage/purchases/" . $product->purchase->image) . '" alt="image">
                            </span>';
                    }
                    return $product->purchase->product . ' ' . $image;
                }
                return '';
            })

            ->addColumn('category', function ($product) {
                if (!empty($product->purchase->category)) {
                    return $product->purchase->category->name;
                }
                return '';
            })

            ->addColumn('price', function ($product) {
                return settings('app_currency', '$') . ' ' . $product->price;
            })

            ->addColumn('quantity', function ($product) {
                if (!empty($product->purchase)) {
                    $qty = $product->purchase->quantity;

                    if ($qty <= 0) {
                        return $qty . ' <span class="badge bg-danger">Out of Stock</span>';
                    } elseif ($qty <= 5) {
                        return $qty . ' <span class="badge bg-warning text-dark">Only ' . $qty . ' boxes left</span>';
                    }

                    return $qty;
                }
                return '-';
            })

            ->addColumn('expiry_date', function ($product) {
                if (!empty($product->purchase)) {
                    return date_format(date_create($product->purchase->expiry_date), 'd M, Y');
                }
                return '-';
            })

            ->addColumn('action', function ($row) {
                $editbtn = '<a href="' . route("products.edit", $row->id) . '" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                $deletebtn = '<a data-id="' . $row->id . '" data-route="' . route('products.destroy', $row->id) . '" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';

                if (!auth()->user()->hasPermissionTo('edit-product')) {
                    $editbtn = '';
                }
                if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                    $deletebtn = '';
                }
                return $editbtn . ' ' . $deletebtn;
            })

            ->rawColumns(['product', 'quantity', 'action'])
            ->make(true);
    }

    return view('admin.products.outstock', compact('title'));
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Product::findOrFail($request->id)->delete();
    }
}
