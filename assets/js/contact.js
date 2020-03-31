'use strict';

const contact = () => {
    const d = document;
    const sendUrl = 'sendmsg.php';
    const submit = d.querySelector('#submitForm');
    const radioBtn = d.querySelector('#radio-toolbar');
    const buttons = d.getElementsByName("reason");
    const message = d.querySelector('#message');
    var  messageSuccess = d.querySelector('#messageSuccess');

    var name = d.querySelector('#name');
    var email = d.querySelector('#email');
    var phone = d.querySelector('#phone');
    var website = d.querySelector('#web');
    var notice = d.querySelector('#notice');
    var sendEmail = {};
    var sendStatus = {
        name: false,
        email: false,
        comments: false
    };
    sendEmail.reason = 'message';
    sendEmail.token = d.querySelector('#token').value;


    var comments = d.querySelector("textarea");
    var output = d.querySelector("#length");




    name.addEventListener('input', evt => {
        const value = name.value.trim();

        if (value) {
            notice.style.display = "none";
            sendEmail.name = name.value;
            sendStatus.name = true;
        } else {
            notice.style.display = "block";
            notice.textContent = "Name is Required";
        }

    });

    const emailIsValid = (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    email.addEventListener('change', () => {
        var status = emailIsValid(email.value);
        console.log('Email Address', email.value, 'Status', status);
        if (!status) {
            email.value = "";
            notice.style.display = "block";
            notice.textContent = "Email Address Invalid!";
        } else {
            notice.style.display = "none";
            sendEmail.email = email.value;
            sendStatus.email = true;
        }
    });


    /*
     * Selection Element
     */
    buttons.forEach((value, index) => {
        //console.log(value, index);
        buttons[index].addEventListener('change', (e) => {
            sendEmail.reason = e.target.value;
            //console.log('Reason:', sendEmail.reason);
        });
    });


    comments.addEventListener("input", evt => {
        output.textContent = comments.value.length;
        const value = comments.value.trim();

        if (value) {
            notice.style.display = "none";
            sendEmail.comments = comments.value;
            sendStatus.comments = true;
        } else {
            notice.style.display = "block";
            notice.textContent = "Comment is Required!";
        }
    });





    /* Success function utilizing FETCH */
    const sendUISuccess = function (result) {
        //console.log('Result', result);
        if (result) {
            d.querySelector('#recaptcha').style.display = "none";
            submit.style.display = "none";
            notice.style.display = "block";

            notice.textContent = "Email Successfully Sent!";
            notice.style.color = "green";
            notice.style.fontSize = "xx-large";
            messageSuccess.style.display = "block";
            d.querySelectorAll('form > *').forEach(function (a) {
                a.disabled = true;
            });
        }
    };

    /* If Database Table fails to update data in mysql table */
    const sendUIError = function (error) {
        console.log("Database Table did not load", error);
    };

    const handleSaveErrors = function (response) {
        if (!response.ok) {
            throw (response.status + ' : ' + response.statusText);
        }
        return response.json();
    };

    const saveRequest = (sendUrl, succeed, fail) => {
        //const data = {username: 'example'};
        fetch(sendUrl, {
            method: 'POST', // or 'PUT'
            body: JSON.stringify(sendEmail)

        })
                .then((response) => handleSaveErrors(response))
                .then((data) => succeed(data))
                .catch((error) => fail(error));
    };



    submit.addEventListener('click', (e) => {
        e.preventDefault();
        sendEmail.phone = phone.value;
        sendEmail.website = website.value;
        sendEmail.response = submit.getAttribute('data-response');
        //console.log(sendEmail, sendStatus);
        if (sendStatus.name && sendStatus.email && sendStatus.comments) {
            saveRequest(sendUrl, sendUISuccess, sendUIError);
        } else {
            notice.style.display = "block";
            notice.textContent = "Name, Email, and Message Required!";
        }

    }, false);


};


contact();
