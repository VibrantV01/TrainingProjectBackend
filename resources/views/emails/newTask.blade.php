<!DOCTYPE html>
<html>
<head>
    <style>
        .content{
            margin: auto,
        }
        title {text-align: center;}
        div {text-align: center;}
        p {text-align: center;}
        h6 {text-align: left;}
        h3 {text-align: left;}
    </style>
    <title>New Task Added</title>
</head>
<body>
    <h6>Dear {{$user->name}},<h6><br/><br/><br/>
<div>
    <p>New task is assigned to you, please visit you page and take necessary actions. </p>
    <p>Details of the task follow.</p>
    <p>Task title: {{$task->title}}</p>
    <p>Task description: {{$task->description}}</p>
    <p>Task due date: {{$task->due_date}}</p>
</div>
<h6>Regards,</h6>
<h6>yourDashboard</h6>
<body>    
</html>    