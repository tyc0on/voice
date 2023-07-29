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
    - when called updates inst.user_id to the user_id of the user that called it


Colab connected indicator
- use JavaScript to ping ngrok url every minute for status
- if status is 200 then show connected via green icon
- if status is 404 then show disconnected via red icon with button to "Open in Colab" link


*/