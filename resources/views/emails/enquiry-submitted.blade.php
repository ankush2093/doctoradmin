<!DOCTYPE html>
<html>
<head>
    <title>Booking Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        
        <h2 style="color: #f4364f;">
            {{ $isAdmin ? 'New Booking Enquiry Received' : 'Thank You for Your Enquiry!' }}
        </h2>
        
        <p>
            Dear {{ $isAdmin ? 'Admin' : ($data['name'] ?? 'Customer') }},
        </p>
        
        <p>
            @if($isAdmin)
                You have received a new booking enquiry from your website.
            @else
                Thank you for contacting us. Our team will get back to you shortly.
            @endif
        </p>

        <h3 style="border-bottom: 2px solid #f4364f; padding-bottom: 5px;">
            Enquiry Details:
        </h3>

        <table style="width: 100%; border-collapse: collapse;">
            
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Name:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $data['name'] ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Email:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $data['email'] ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phone:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $data['phone'] ?? 'N/A' }}</td>
            </tr>

            @if(!empty($data['city']))
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>City:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $data['city'] }}</td>
            </tr>
            @endif

            @if(!empty($data['trainerType']))
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Trainer Type:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ ucfirst($data['trainerType']) }}</td>
            </tr>
            @endif

            @if(!empty($data['trainingType']))
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Training Type:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ ucfirst($data['trainingType']) }}</td>
            </tr>
            @endif

            @if(!empty($data['message']))
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>Message:</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $data['message'] }}</td>
            </tr>
            @endif

        </table>

        <p style="margin-top: 20px;">
            Regards,<br>
            <strong>Your Team</strong>
        </p>
    </div>
</body>
</html>