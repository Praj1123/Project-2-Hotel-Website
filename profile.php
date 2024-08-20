<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Profile</title>
    <style>
    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
    }
      </style>
  </head>
  <body>
    <section style="background-color: #eee;">
        <div class="container py-5">
          <div class="row">
            <div class="col">
              <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="https://asraaigl.com/">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><a href="profile.php">User Profile</a></li>
                </ol>
              </nav>
            </div>
          </div>
          <div class="alert alert-success" role="alert" id='alert' style='display:none'>Couldn't fetch Profile Details. Need to <a href='signin.php' onclick="saveRedirectUrl()"><b>Sign in</b></a></div>
          <div class="row">
            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-body text-center">
                  <div>
                  <img id='profile' src="https://www.citypng.com/public/uploads/preview/profile-user-round-red-icon-symbol-download-png-11639594337tco5j3n0ix.png" alt="avatar"
                    class="img-fluid" style="width: 150px;position:relative;height: 150px;border-radius: 50%">
                    <button class='btn btn-danger mt-5' id='open_file' style='position:absolute;bottom:100px;right:120px;border-radius:20px;font-size:large'><b>+</b></button>
                    <input type='file' style='display:none' id='file_input'></input>
                  </div>
                  <h5 class="my-3" id='user_name'>---</h5>
                  <p class="text-muted mb-1" id='user_email'>---</p>
                </div>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="card mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0">Full Name</p>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id='user_name1' class="form-control" disabled />
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0">Email</p>
                    </div>
                    <div class="col-sm-9">
                      <input type="email" id='user_email1' class="form-control" disabled />
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0">Phone</p>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id='user_phone_number' class="form-control" disabled />
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0">Update Password</p>
                    </div>
                    <div class="col-sm-9">
                      <input type="password" id='user_password' class="form-control" disabled />
                    </div>
                  </div>
                </div>
              </div>
              <button class='btn btn-danger mt-5' id='update'>Update Profile</button>
              <button class='btn btn-success mt-5' id='save' style='display:none'>Save</button>
              <button class='btn btn-secondary mt-5' id='cancel' style='display:none'>Cancel</button>
              <button class='btn btn-outline-danger mt-5' id='signOut' style='display:block;float:right'>Sign out</button>
            </div>
          </div>
        </div>
        <div class="container mb-5">
    <div class="table-responsive">
      <h2 class='mb-3' style='color:red'> Your Recent Donations</h2>
        <table id="donationsTable" class='table table-bordered'>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Donated Amount</th>
                    <th>Donation Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

      </div>
      </div>
    </section>
      <script>

document.getElementById('open_file').addEventListener('click', function() {
    document.getElementById('file_input').click();
});


function sendFileDataToServer(profile) {
    const uniqueId = localStorage.getItem('dntidd');
    if (!uniqueId) {
        console.error('Unique ID not found in local storage.');
        return;
    }

    const xhr = new XMLHttpRequest();
    const url = 'upload.php';
    const params = `uniqueId=${encodeURIComponent(uniqueId)}&profile=${encodeURIComponent(profile)}`;

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    console.log('Update Successful:', response.message);
                    document.getElementById('alert').innerHTML = 'Profile update successfully.'
                } else {
                    console.error('Error:', response.message);
                    alert(response.message);
                }
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                alert('Failed to parse JSON');
            }
        } else {
            console.error('Request failed with status:', xhr.status);
            alert('Request failed with status ' + xhr.status);
        }
    };

    xhr.onerror = function () {
        console.error('Request failed.');
        alert('Request failed.');
    };

    xhr.send(params);
}

document.getElementById('file_input').addEventListener('change', function() {
    const file = document.getElementById('file_input').files[0];
    if (file) {
        // Check if the file type is an image
        const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (validImageTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const fileURL = e.target.result;
                console.log(fileURL);
                const imgElement = document.getElementById('profile');
                imgElement.src = fileURL; 
                sendFileDataToServer(fileURL);
            };
            reader.readAsDataURL(file);
        } else {
            alert('Please upload a valid image file (JPEG, PNG, JPG).');
            document.getElementById('file_input').value = ''; // Clear the input
        }
    }
});



        fetchUserData();  
        function fetchUserData() {
            const uniqueId = localStorage.getItem('dntidd');
            if (!uniqueId) {
                console.error('Unique ID not found in local storage.');
                return;
            }
        
            const xhr = new XMLHttpRequest();
            const url = 'fetch_user_data.php';
            const params = `uniqueId=${encodeURIComponent(uniqueId)}`;
        
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        const user_data = response.data;
                        document.getElementById('user_name').innerText = user_data.fullName;
                        document.getElementById('user_name1').value = user_data.fullName;
                        document.getElementById('user_email').innerText = user_data.email;
                        document.getElementById('user_email1').value = user_data.email;
                        document.getElementById('user_phone_number').value = user_data.phoneNumber;
                        document.getElementById('user_password').value = user_data.password;
                        document.getElementById('profile').src = user_data.profile;
                    } else {
                        console.error('Error:', response.message);
                    }
                } else {
                    console.error('Request failed with status:', xhr.status);
                }
            };
        
            xhr.onerror = function () {
                console.error('Request failed.');
            };
        
            xhr.send(params);
        }

        document.getElementById('update').addEventListener('click', () => {
            document.getElementById('save').style.display = 'inline';
            document.getElementById('cancel').style.display = 'inline';
            document.getElementById('update').style.display = 'none';
            document.getElementById('user_name1').disabled = false;
            document.getElementById('user_email1').disabled = false;
            document.getElementById('user_phone_number').disabled = false;
            document.getElementById('user_password').disabled = false;
        });
        document.getElementById('cancel').addEventListener('click', () => {
            document.getElementById('save').style.display = 'none';
            document.getElementById('cancel').style.display = 'none';
            document.getElementById('update').style.display = 'inline';
            fetchUserData(); // Refresh the data to undo changes
        });
    document.getElementById('save').addEventListener('click', () => {
    const name = document.getElementById('user_name1').value;
    const email = document.getElementById('user_email1').value;
    const phoneNumber = document.getElementById('user_phone_number').value;
    var password = document.getElementById('user_password').value;
    console.log(name,email,phoneNumber,password)

    const uniqueId = localStorage.getItem('dntidd');
    if (!uniqueId) {
        console.error('Unique ID not found in local storage.');
        return;
    }

    const xhr = new XMLHttpRequest();
    const url = 'update_user_data.php';
    const params = `uniqueId=${encodeURIComponent(uniqueId)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phoneNumber=${encodeURIComponent(phoneNumber)}&password=${encodeURIComponent(password)}`;

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    console.log('Update Successful:', response.message);
                    document.getElementById('update').style.display = 'inline';
                    document.getElementById('save').style.display = 'none';
                    document.getElementById('cancel').style.display = 'none';
                    document.getElementById('user_name1').contentEditable = 'false';
                    document.getElementById('user_email1').contentEditable = 'false';
                    document.getElementById('user_phone_number').contentEditable = 'false';
                    document.getElementById('user_password').contentEditable = 'false';
                } else {
                    console.error('Error:', response.message);
                }
            } catch (e) {
                console.error('Failed to parse JSON:', e);
            }
        } else {
            console.error('Request failed with status:', xhr.status);
        }
    };

    xhr.onerror = function () {
        console.error('Request failed.');
    };

    xhr.send(params);
});

fetch_donation_table()
function fetch_donation_table() {
    const uniqueId = localStorage.getItem('dntidd');
    if (!uniqueId) {
        console.error('Unique ID not found in local storage.');
        return;
    }

    const xhr = new XMLHttpRequest();
    const url = 'fetch_donation_table.php';
    const params = `uniqueId=${encodeURIComponent(uniqueId)}`;

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    const donations = response.data;
                    const table = document.getElementById('donationsTable');
                    const tbody = table.querySelector('tbody');
                    tbody.innerHTML = ''; // Clear existing content

                    // Check if donations is an array
                    if (Array.isArray(donations)) {
                        donations.forEach(donation => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${donation.uniqueId}</td>
                                <td>${donation.name}</td>
                                <td>${donation.phoneNumber}</td>
                                <td>${donation.donatedAmount}</td>
                                <td>${donation.created_at}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        console.error('Invalid data format:', donations);
                    }
                } else {
                    console.error('Error:', response.message);
                }
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.error('Response text:', xhr.responseText); // Log raw response text
            }
        } else {
            console.error('Request failed with status:', xhr.status);
        }
    };

    xhr.onerror = function () {
        console.error('Request failed.');
    };

    xhr.send(params);
}
status()

function status(){
  if(localStorage.getItem('dntidd')){
    document.getElementById('alert').style.display='block'
    document.getElementById('alert').innerHTML='Profile fetch Successfully'
  }else{
    document.getElementById('alert').style.display='block'
  }
}

document.getElementById('signOut').addEventListener('click', () => {
  if(confirm('Log out lead to an sign in again!')){
    localStorage.removeItem('dntidd');
    alert('Log out successfully.... Redirecting to home page')
  window.location.href='https://asraaigl.com/'
}})

  

function saveRedirectUrl() {
    // Save the current URL or the URL where you want to redirect after sign-in
    sessionStorage.setItem('redirectURL', 'profile.php.php');
}


      </script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
