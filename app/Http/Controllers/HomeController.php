<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // /**
    //  * Show the application dashboard.
    //  *
    //  * @return \Illuminate\Contracts\Support\Renderable
    //  */
    public function index()
    {
        $slides = Slide::where('status', 1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        return view('index', compact('slides', 'categories'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^[0-9]{9,10}$/'],
            'comment' => 'required'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $results = Product::where('name', 'LIKE', "%{$query}%")
                        ->take(8)
                        ->get();

        return response()->json($results);
    }

    public function about_us()
    {
        $map = "https://maps.app.goo.gl/SFQLcT2zEnc92BbR9"; 

        return view('about-us', compact('map'));
    }
}
