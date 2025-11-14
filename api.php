<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Enable errors for debugging
ini_set('display_errors',1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("sql305.infinityfree.com","if0_40416312","NJyotsna22","if0_40416312_bus_booking");
if($conn->connect_error){ 
    die(json_encode(["status"=>"error","msg"=>"DB Connection Failed"])); 
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

/* -------------------- REGISTER -------------------- */
if($action == 'register'){
    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password']; // plain text

    // Check if email exists
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($res->num_rows > 0){
        echo json_encode(["status"=>"error","msg"=>"Email already registered"]);
        exit;
    }

    // Insert user
    if($conn->query("INSERT INTO users(name,email,password) VALUES('$name','$email','$password')")){
        echo json_encode(["status"=>"success","msg"=>"Registered successfully"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Database insert failed"]);
    }
    exit;
}

/* -------------------- LOGIN -------------------- */
if($action == 'login'){
    $email = $conn->real_escape_string($data['email']);
    $password = $conn->real_escape_string($data['password']);

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        echo json_encode(['status'=>'success','user'=>$user]);
    } else {
        echo json_encode(['status'=>'error','msg'=>'Invalid credentials']);
    }
    exit;
}


/* -------------------- GET BUSES -------------------- */
if($action == 'getBuses'){
    $from = $data['from'] ?? '';
    $to = $data['to'] ?? '';
    $date = $data['date'] ?? ''; // Optional, can be used if you store travel dates

    // Start building query
    $query = "SELECT * FROM buses WHERE 1=1";

    // Filter by source
    if(!empty($from)){
        $from = $conn->real_escape_string($from);
        $query .= " AND source LIKE '%$from%'";
    }

    // Filter by destination
    if(!empty($to)){
        $to = $conn->real_escape_string($to);
        $query .= " AND destination LIKE '%$to%'";
    }

    // Optional: filter by date if you track booked dates (if not needed, ignore)
    // Example if buses have a schedule_date column
    // if(!empty($date)){
    //     $date = $conn->real_escape_string($date);
    //     $query .= " AND schedule_date = '$date'";
    // }

    $result = $conn->query($query);
    $buses = [];
    while($row = $result->fetch_assoc()){
        $buses[] = $row;
    }
    echo json_encode($buses);
    exit;
}


/* -------------------- BOOK SEAT -------------------- */
if($action == 'bookSeat'){
    $user_id = $data['user_id'];
    $bus_id = $data['bus_id'];
    $seat_no = $data['seat_no'];

    // Check seat availability
    $bus_res = $conn->query("SELECT seats_available,seats_total FROM buses WHERE bus_id=$bus_id");
    if($bus_res->num_rows == 0){
        echo json_encode(["status"=>"error","msg"=>"Bus not found"]);
        exit;
    }
    $bus = $bus_res->fetch_assoc();
    if($seat_no < 1 || $seat_no > $bus['seats_total']){
        echo json_encode(["status"=>"error","msg"=>"Invalid seat number"]);
        exit;
    }
    if($bus['seats_available'] < 1){
        echo json_encode(["status"=>"error","msg"=>"No seats available"]);
        exit;
    }

    // Insert booking
    if($conn->query("INSERT INTO bookings(user_id,bus_id,seat_no) VALUES($user_id,$bus_id,$seat_no)")){
        // Reduce available seats
        $conn->query("UPDATE buses SET seats_available=seats_available-1 WHERE bus_id=$bus_id");
        echo json_encode(["status"=>"success","msg"=>"Seat booked"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Booking failed"]);
    }
    exit;
}

/* -------------------- GET USER BOOKINGS -------------------- */
if($action == 'getMyBookings'){
    $user_id = $data['user_id'];
    $res = $conn->query("
        SELECT b.booking_id, bu.bus_name, bu.source, bu.destination, bu.departure_time, b.seat_no, b.booking_time
        FROM bookings b
        JOIN buses bu ON b.bus_id = bu.bus_id
        WHERE b.user_id=$user_id
        ORDER BY b.booking_time DESC
    ");
    $bookings = [];
    while($row = $res->fetch_assoc()){
        $bookings[] = $row;
    }
    echo json_encode($bookings);
    exit;
}

// Get single ticket details
if($action == 'getTicket'){
    $booking_id = intval($data['booking_id']); // sanitize input
    $res = $conn->query("
        SELECT b.booking_id, bu.bus_name, bu.source, bu.destination, bu.departure_time, b.seat_no, b.booking_time
        FROM bookings b
        JOIN buses bu ON b.bus_id = bu.bus_id
        WHERE b.booking_id=$booking_id
    ");
    $ticket = $res->fetch_assoc();
    if(!$ticket){
        echo json_encode(['error'=>'Ticket not found']);
    } else {
        echo json_encode($ticket);
    }
    exit;
}


/* -------------------- ADMIN: ADD BUS -------------------- */
if($action == 'addBus'){
    $bus_name = $data['bus_name'];
    $source = $data['source'];
    $destination = $data['destination'];
    $departure_time = $data['departure_time'];
    $seats_total = $data['seats_total'];

    if($conn->query("INSERT INTO buses(bus_name,source,destination,departure_time,seats_total,seats_available)
        VALUES('$bus_name','$source','$destination','$departure_time',$seats_total,$seats_total)")){
        echo json_encode(["status"=>"success","msg"=>"Bus added"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Failed to add bus"]);
    }
    exit;
}

/* -------------------- ADMIN: UPDATE BUS -------------------- */
if($action == 'updateBus'){
    $bus_id = $data['bus_id'];
    $bus_name = $data['bus_name'];
    $source = $data['source'];
    $destination = $data['destination'];
    $departure_time = $data['departure_time'];
    $seats_total = $data['seats_total'];

    // Update seats_available if seats_total changed
    $bus_res = $conn->query("SELECT seats_total,seats_available FROM buses WHERE bus_id=$bus_id");
    $bus = $bus_res->fetch_assoc();
    $diff = $seats_total - $bus['seats_total'];
    $new_available = $bus['seats_available'] + $diff;
    if($new_available < 0) $new_available = 0;

    if($conn->query("UPDATE buses SET bus_name='$bus_name',source='$source',destination='$destination',departure_time='$departure_time',seats_total=$seats_total,seats_available=$new_available WHERE bus_id=$bus_id")){
        echo json_encode(["status"=>"success","msg"=>"Bus updated"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Failed to update bus"]);
    }
    exit;
}

/* -------------------- ADMIN: DELETE BUS -------------------- */
if($action == 'deleteBus'){
    $bus_id = $data['bus_id'];
    if($conn->query("DELETE FROM buses WHERE bus_id=$bus_id")){
        echo json_encode(["status"=>"success","msg"=>"Bus deleted"]);
    } else {
        echo json_encode(["status"=>"error","msg"=>"Failed to delete bus"]);
    }
    exit;
}

/* -------------------- DEFAULT -------------------- */
echo json_encode(["status"=>"error","msg"=>"Invalid action"]);
exit;
?>
