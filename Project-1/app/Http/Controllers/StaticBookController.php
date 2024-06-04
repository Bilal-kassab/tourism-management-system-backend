<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\BookStaticTripRequest;
use App\Http\Requests\Book\CheckStaticTripRequest;
use App\Http\Requests\Book\EditStaticTripRequest;
use App\Http\Requests\Trip\DynamicTripRequest;
use App\Http\Requests\Trip\StoreStaticTripRequest;
use App\Http\Requests\Trip\UpdateStaticTripRequest;
use App\Models\Bank;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\BookingStaticTrip;
use App\Models\BookPlace;
use App\Models\BookPlane;
use App\Models\Place;
use App\Models\PlaneTrip;
use App\Models\Room;
use App\Models\StaticTripRoom;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class StaticBookController extends Controller
{


    private $bookrepository;

    public function __construct(BookRepositoryInterface $bookrepository)
    {
        $this->bookrepository = $bookrepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $static_trips=$this->bookrepository->index();
            return response()->json([
                'data'=>$static_trips
            ],200);
        }catch(Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
            ],404);

        }
    }

    public function store_Admin(StoreStaticTripRequest $request)
    {
        $data=[
            'source_trip_id'=>$request->source_trip_id,
            'destination_trip_id'=>$request->destination_trip_id,
            'hotel_id'=>$request->hotel_id,
            'trip_name'=>$request->trip_name,
            'ratio'=>$request->ratio,
            'number_of_people'=>$request->number_of_people,
            'trip_capacity'=>$request->trip_capacity,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'trip_note'=>$request->trip_note,
            'places'=>$request->places,
            'activities'=>$request->activities,##
            'plane_trip'=>$request->plane_trip,
            'plane_trip_away'=>$request->plane_trip_away,
        ];
        $static_book=$this->bookrepository->store_Admin($data);
        if($static_book == 1){
            return response()->json([
                'message'=>'there is not enough room in this hotel',
            ],400);
        }
        if($static_book == 2){
            return response()->json([
                'message' => 'the seats of the going trip plane lower than number of person'
            ], 400);
        }
        if($static_book == 3){
            return response()->json([
                'message' => 'the seats of the return trip plane lower than number of person'
            ], 400);
        }
        if($static_book == 4){
            return response()->json([
                'message' => 'Failed to create a trip',
            ], 400);
        }

        return response()->json([
            'data'=>$static_book
        ],200);
    }
    public function update_Admin(UpdateStaticTripRequest $request,$id)
    {
        $data=[
            'hotel_id'=>$request->hotel_id,
            'trip_name'=>$request->trip_name,
            'price'=>$request->price,
            'number_of_people'=>$request->add_new_people,
            'trip_capacity'=>$request->trip_capacity,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'trip_note'=>$request->trip_note,
            'places'=>$request->places,
            'plane_trip'=>$request->plane_trip,
            'plane_trip_away'=>$request->plane_trip_away,
        ];
        $booking= Booking::findOrFail($id);
        if(auth()->id() != $booking->user_id)
        {
            return response()->json([
                'message'=>'You do not have the permission',
            ],200);
        }
        $edit=$this->bookrepository->editAdmin($data,$id);
        if($edit === 1){
            return response()->json([
                'message'=>'there is not enough room in this hotel',
            ],400);
        }
        if($edit === 2){
            return response()->json([
                'message' => 'the seats of the going trip plane lower than number of person'
            ], 400);
        }
        if($edit === 3){
            return response()->json([
                'message' => 'the seats of the return trip plane lower than number of person'
            ], 400);
        }
        if($edit === 4)
        {
            return response()->json([
                'message'=>'updated failed'
            ],404);
        }
        if ($edit === 5)
        {
            return response()->json([
                'message' => 'You should choose a period similar to the ancient period'
            ], 400);
        }
        return response()->json([
            'message'=> 'booking has been updated successfully',
            'data'=>$edit,
          ],200);
    }

    public function showStaticTrip($id)
    {
        $trip=$this->bookrepository->showStaticTrip($id);
        if($trip===1)
        {
            return response()->json([
                'message'=>'Not Found'
            ],404);
        }
        return response()->json(['data'=>$trip],200);

    }

    public function checkStaticTrip(CheckStaticTripRequest $request,$id):JsonResponse
    {
        try{
            $val=$this->bookrepository->checkStaticTrip($request->all(),$id);
            if($val==1){
                return response()->json([
                    'message'=>'there are not enough members',
                ],400);
            }
            if($val==2){
                return response()->json([
                    'message'=>'there are not enough rooms',
                ],400);
            }
            if($val==3){
                return response()->json([
                    'message'=>'Error!',
                ],400);
            }
            return response()->json([
                'data'=>$val
            ],200);
        }catch(Exception $exception){
            return response()->json([
                'message'=>$exception->getMessage(),
            ],400);
        }
    }

    public function bookStaticTrip(BookStaticTripRequest $request):JsonResponse
    {
        $val=$this->bookrepository->bookStaticTrip($request->all());
        if($val==1)
        {
            return response()->json([
                'message'=>'You dont have the money for this trip',
            ],400);
        }
        return response()->json([
            'message'=>'Enjoy your trip'
        ],200);
    }

    public function showAllMyStaicTrips()
    {
        //$static_trip=Booking::with('user_rooms')->get();
        $static_trip=BookingStaticTrip::with('static_trip:id,trip_name,trip_capacity,start_date,end_date,stars,trip_note','rooms:id,capacity')
                                        ->select('id','user_id','static_trip_id','number_of_friend','book_price')
                                        ->where('user_id',auth()->id())
                                        ->get();

        return response()->json([
            'data'=>$static_trip,
        ],200);
    }

    public function editBook(EditStaticTripRequest $request,$id)
    {
        try{
            $data=[
                'number_of_friend'=>$request['new_number_of_friend'],
                'discount'=>$request['discount']
            ];
           $val=$this->bookrepository->editBook($data,$id);
           if($val==1){
                return response()->json([
                    'message'=>'there are not enough members',
                ],400);
            }
            if($val==2){
                return response()->json([
                    'message'=>'there are not enough rooms',
                ],400);
            }
            if($val==3){
                return response()->json([
                    'message'=>'You dont have the money for this trip',
                ],400);
            }
            if($val==5){
                return response()->json([
                    'message'=>'Error int this trip',
                ],400);
            }
            if($val==10){
                return response()->json([
                    'message'=>'Fail because of invaild Date'
                ]);
            }

            return response()->json([
                'message'=>'Changes saved successfully'
            ],200);
        }catch(Exception $exception){
            return response()->json([
                'message'=>$exception->getMessage()
            ]);
        }
    }

    public function deleteBook($id):JsonResponse
    {
        $val=$this->bookrepository->deleteBook($id);
        if($val==1){
            return response()->json([
                'message'=>'Fail because of invaild Date'
            ],400);
        }

        if($val==3){
            return response()->json([
                'message'=>'Not Found',
            ],404);
        }
        return response()->json([
            'message'=>'Deleted Done and your money has returned to your Account',
        ],400);
    }
}
