import {show_modal, hide_modal, show_password, create_alert_node} from './module.posw.js';

// remove user
const table  = document.querySelector('table.table');
const modal = document.querySelector('.modal');
const modal_content = modal.querySelector('.modal__content');

const tbody = table.querySelector('tbody');
tbody.addEventListener('click', e => {
    let target = e.target;

    if (target.getAttribute('id') !== 'remove-user') target = target.parentElement;
    if (target.getAttribute('id') !== 'remove-user') target = target.parentElement;

    if (target.getAttribute('id') === 'remove-user') {
        e.preventDefault();

        // show modal
        show_modal(modal, modal_content);

        // append data for delete user in modal
        modal_content.querySelector('input[name="user_id"]').value = target.dataset.userId;
        modal_content.querySelector('div.modal__body p strong').innerText = target.dataset.fullName;
    }
});

// close modal
modal_content.querySelector('a#btn-close').addEventListener('click', e => {
    e.preventDefault();

    // hide modal
    hide_modal(modal, modal_content);

    // reset modal
    modal_content.querySelector('input[name="password"]').value = '';
    const small = modal_content.querySelector('small.form-message')
    if (small !== null) {
        small.remove();
    }
});

// remove user in db
modal_content.querySelector('a#remove-user-in-db').addEventListener('click', e => {
    e.preventDefault();

    const password = modal_content.querySelector('input[name="password"]').value;
    const user_id = modal_content.querySelector('input[name="user_id"]').value;
    const csrf_name = table.dataset.csrfName;
    const csrf_value = table.dataset.csrfValue;

    // reset form message
    const small = modal_content.querySelector('small.form-message');
    if (small !== null) {
        small.remove();
    }

    // loading
    e.target.classList.add('btn--disabled');
    e.target.nextElementSibling.classList.remove('d-none');

    fetch('/admin/hapus_pengguna_di_db', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `${csrf_name}=${csrf_value}&user_id=${user_id}&password=${password}`
    })
    .finally(() => {
        // loading
        e.target.classList.remove('btn--disabled');
        e.target.nextElementSibling.classList.add('d-none');
    })
    .then(response => {
        return response.json();
    })
    .then(json => {
        // set new csrf hash to table tag
        if (json.csrf_value !== undefined) {
            table.dataset.csrfValue = json.csrf_value;
        }

        // if success remove user
        if (json.status === 'success') {
            document.querySelector(`tr#user${user_id}`).remove();
        }
        // else if password sign in user is wrong
        else if (json.status === 'wrong_password') {
            const small = document.createElement('small');
            small.classList.add('form-message');
            small.classList.add('form-message--danger');
            small.innerText = json.message;

            // append message to modal
            modal_content.querySelector('div.modal__body').append(small);

            return false;
        }
        // else if fail remove user
        else if (json.status === 'fail') {
            const alert = create_alert_node(['alert--warning', 'mb-3'], json.message);

            // append alert to before div.main__box element
            document.querySelector('main.main > div').insertBefore(alert, document.querySelector('div.main__box'));
        }

        if (json.status === 'success' || json.status === 'fail') {
            // hide modal
            hide_modal(modal, modal_content);

            // reset modal
            modal_content.querySelector('input[name="password"]').value = ''
        }
    })
    .catch(error => {
        console.error(error);
    });
});

// show password
document.querySelector('.modal a#show-password').addEventListener('click', show_password);
