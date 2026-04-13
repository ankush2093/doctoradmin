<?php

namespace App\Http\Controllers;

use App\Models\BookingEnquiry;
use Illuminate\Http\Request;
use App\Mail\EnquirySubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;

class BookingEnquiryController extends Controller
{
    // Create new booking enquiry (public)
    // public function create(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'name'    => 'nullable|string',
    //             'email'   => 'nullable|email',
    //             'phone'   => 'nullable|string',
    //             'city' => 'nullable|string',
    //             'trainerType' => 'nullable|string',
    //             'trainingType' => 'nullable|string',
    //             'message' => 'nullable|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //         }

    //         $data = $request->all();
    //         $data['isActive'] = true;
    //         $bookingEnquiry = BookingEnquiry::create($data);

    //         return response()->json(['success' => true, 'message' => 'Booking enquiry sent successfully.', 'bookingEnquiry' => $bookingEnquiry], 201);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    // public function create(Request $request)
    // {
    //     try {

    //         // ✅ YAHAN ADD KARO (sabse upar)
    //         Mail::raw('Test Mail', function ($msg) {
    //             $msg->to('karanbgs2000@gmail.com')->subject('Test Mail');
    //         });

    //         dd('Mail Sent Successfully');

    //         // 👇 Ye sab code temporarily neeche rehne do (run nahi hoga)
    //         $validator = Validator::make($request->all(), [
    //             'name'    => 'required|string',
    //             'email'   => 'required|email',
    //             'phone'   => 'required|string',
    //         ]);

    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //     }
    // }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string',
                'email'   => 'required|email',
                'phone'   => 'required|string',
                'city' => 'nullable|string',
                'trainerType' => 'nullable|string',
                'trainingType' => 'nullable|string',
                'message' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Save data
            $data = $request->all();
            $data['isActive'] = true;

            $bookingEnquiry = BookingEnquiry::create($data);

            // ✅ Email data prepare
            $emailData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'trainerType' => $data['trainerType'],
                'trainingType' => $data['trainingType'],
                'message' => $data['message'],
                'id' => $bookingEnquiry->id
            ];

            // ✅ Send mail to user
            Mail::to($data['email'])->send(new EnquirySubmitted($emailData));

            // ✅ Send mail to admin
            Mail::to('karanbgs2000@gmail.com')
                ->cc('karanbgs2000@gmail.com')
                ->send(new EnquirySubmitted($emailData, true));

            return response()->json([
                'success' => true,
                'message' => 'Booking enquiry sent successfully.',
                'bookingEnquiry' => $bookingEnquiry
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // List all enquiries (admin)
    public function getAll()
    {
        try {
            $bookingEnquiries = BookingEnquiry::where('isActive', true)->orderBy('created_at', 'desc')->get();
            return response()->json(['success' => true, 'message' => 'Booking enquiries retrieved successfully.', 'bookingEnquiries' => $bookingEnquiries], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Show enquiry by id
    public function getById($id)
    {
        try {
            $bookingEnquiry = BookingEnquiry::where('id', $id)->where('isActive', true)->first();
            if (!$bookingEnquiry) {
                return response()->json(['success' => false, 'message' => 'Enquiry not found or inactive'], 404);
            }
            return response()->json(['success' => true, 'message' => 'Booking enquiry retrieved successfully.', 'bookingEnquiry' => $bookingEnquiry], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete enquiry
    public function delete($id)
    {
        try {
            $bookingEnquiry = BookingEnquiry::find($id);
            if (!$bookingEnquiry) {
                return response()->json(['success' => false, 'message' => 'Enquiry not found'], 404);
            }

            $bookingEnquiry->update(['isActive' => false]);

            return response()->json(['success' => true, 'message' => 'Booking enquiry deleted successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
