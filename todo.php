<?php

/* my unofficial todo list

Connect to colab
- on user signup asign a random app{rand}.ipynb
- get {rand} from db and update db as used by user_id
- on user login get {rand} from db and add to $_SESSION['colab'] = {rand}

- create a database called inst with id and user_id default NULL
- make a function that
    - checks if $_SESSION['id'] has not already been assigned in user_id
    - if not then
        - checks if there is an inst with user_id = NULL
        - if so then
            - updates inst.user_id to $_SESSION['id']
            - updates $_SESSION['colab'] to inst.id
        - if not then
            - creates a new inst with user_id = $_SESSION['id']
            - updates $_SESSION['colab'] to inst.id


Colab connected indicator
- use JavaScript to ping ngrok url (eg. 4b0e-34-66-187-77.ngrok.io) every minute for status contained in JSON response status = running
- if status is 200 then show connected via green icon
- if timesout in 5 seconds then show disconnected via red icon with link to reconnect


30 July

- use url.php to get updated ngrok url
- then checkServerStatus(url) to get status on start

*/