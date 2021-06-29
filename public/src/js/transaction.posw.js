import {create_alert_node, number_formatter_to_currency, show_modal, hide_modal, show_password} from './module.posw.js';
import flatpickr from 'flatpickr';
import { Indonesian } from "flatpickr/dist/l10n/id.js";

const table = document.querySelector('table.table');
const search_transaction = document.querySelector('a#search-transaction');
const export_transaction_excel = document.querySelector('a#export-transaction-excel');
const result_status = document.querySelector('span#result-status');
const modal = document.querySelector('.modal');
const modal_content = modal.querySelector('.modal__content');


// flatpickr setting
flatpickr('input[name="date_range"]', {
    disableMobile: 'true',
    mode: 'range',
    altInput: true,
    altFormat: 'j M, Y',
    altInputClass: 'form-input form-input--rounded-left hover-cursor-pointer',
    locale: Indonesian,
    onReady: () => {
        // this function for change name for 7th day in 1 week
        document.querySelectorAll('.flatpickr-weekdaycontainer .flatpickr-weekday')[6].innerText = 'Ahad';
    }
});

// search transaction
search_transaction.addEventListener('click', e => {
    e.preventDefault();

    const date_range = document.querySelector('input[name="date_range"]').value;
    const csrf_name = table.dataset.csrfName;
    const csrf_value = table.dataset.csrfValue;

    // if empty date range
    if (date_range.trim() === '') {
        return false;
    }

    // loading
    table.parentElement.nextElementSibling.classList.remove('d-none');
    // disabled button search
    search_transaction.classList.add('btn--disabled');

    fetch('/admin/cari_transaksi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `date_range=${date_range}&${csrf_name}=${csrf_value}`
    })
    .finally(() => {
        // loading
        table.parentElement.nextElementSibling.classList.add('d-none');
        // enabled button search
        search_transaction.classList.remove('btn--disabled');
    })
    .then(response => {
        return response.json();
    })
    .then(json => {
        // set new csrf hash to table tag
        if (json.csrf_value !== undefined) {
            table.dataset.csrfValue = json.csrf_value;
        }

        // if transaction exists
        if (json.transactions_db.length > 0) {
            let tr = '';

            json.transactions_db.forEach((t, i) => {
                // if i is odd number
                if ((i+1)%2 !== 0) {
                    tr += '<tr class="table__row-odd">';
                } else {
                    tr += '<tr>';
                }
                tr += '<td width="10">';

                // if transaction is allow for delete
                if (t.permission_delete === true) {
                    tr += `<div class="form-check">
                            <input type="checkbox" name="transaction_id" data-create-time="${t.waktu_buat}" class="form-check-input" value="${t.transaksi_id}">
                        </div>`;
                }

                tr += `</td>
                    <td width="10"><a href="#" id="show-transaction-detail" data-transaction-id="${t.transaksi_id}" title="Lihat detail transaksi"><svg xmlns="http://www.w3.org/2000/svg" width="21" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg></a></td>

                    <td>${t.product_total||0}</td>
                    <td>${number_formatter_to_currency(parseInt(t.payment_total||0))}</td>`;

                if (t.status_transaksi === 'selesai') {
                    tr += '<td><span class="text-green">Selesai</span></td>';
                } else {
                    tr += '<td><span class="text-red">Belum</span></td>';
                }

                tr += `<td>${t.nama_lengkap}</td><td>${t.indo_create_time}</td></tr>`;
            });

            table.querySelector('tbody').innerHTML = tr;

            // show result status
            result_status.innerText = `1 - ${json.transactions_db.length} dari ${json.transaction_search_total} Total transaksi hasil pencarian`;

            // add dataset type-show and dataset date-range
            table.dataset.typeShow = 'date-range';
            table.dataset.dateRange = date_range;
        }
        // if transaction not exists
        else {
            // inner html message
            table.querySelector('tbody').innerHTML = `<tr class="table__row-odd"><td colspan="7">Transaksi tidak ada.</td></tr>`;

            // show result status
            result_status.innerText = '0 Total transaksi hasil pencarian';
        }

        const limit_message = document.querySelector('span#limit-message');
        // add limit message if transaction search total = product limit && limit message not exists
        if (json.transactions_db.length === json.transaction_limit && limit_message === null) {
            const span = document.createElement('span');
            span.classList.add('text-muted');
            span.classList.add('d-block');
            span.classList.add('mt-3');
            span.setAttribute('id', 'limit-message');
            span.innerHTML = `Hanya ${json.transaction_limit} Transaksi terbaru yang ditampilkan, Pakai fitur <i>Pencarian</i> untuk hasil lebih spesifik!`;
            table.after(span);
        }
        // else if product search total != product limit and limit message exists
        else if (json.transactions_db.length !== json.transaction_limit && limit_message !== null) {
            limit_message.remove();
        }
    })
    .catch(error => {
        console.error(error);
    });
});

// show hide transaction details
const tbody = document.querySelector('table.table tbody');
tbody.addEventListener('click', e => {
    let target = e.target;
    if(target.getAttribute('id') !== 'show-transaction-detail') target = target.parentElement;
    if(target.getAttribute('id') !== 'show-transaction-detail') target = target.parentElement;
    if(target.getAttribute('id') === 'show-transaction-detail') {
        e.preventDefault();

        // if next element sibling exists and next element sibling is tr.table__row-detail, or is mean transaction detail exists in table
        const table_row_detail = target.parentElement.parentElement.nextElementSibling;
        if(table_row_detail !== null && table_row_detail.classList.contains('table__row-detail')) {
            table_row_detail.classList.toggle('table__row-detail--show');

        // else, is mean transaction detail not exists in table
        } else {
            const transaction_id = target.dataset.transactionId;
            const csrf_name = table.dataset.csrfName;
            const csrf_value = table.dataset.csrfValue;

            // loading
            table.parentElement.nextElementSibling.classList.remove('d-none');
            // disabled button search
            search_transaction.classList.add('btn--disabled');

            fetch('/admin/tampil_transaksi_detail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `${csrf_name}=${csrf_value}&transaction_id=${transaction_id}`
            })
            .finally(() => {
                // loading
                table.parentElement.nextElementSibling.classList.add('d-none');
                // enabled button search
                search_transaction.classList.remove('btn--disabled');
            })
            .then(response => {
                return response.json();
            })
            .then(json => {
                // set new csrf hash to table tag
                if (json.csrf_value !== undefined) {
                    table.dataset.csrfValue = json.csrf_value;
                }

                // if exists transaction details
                if (json.transaction_details.length > 0) {
                    let li = '';
                    json.transaction_details.forEach(val => {
                        li += `<li><span class="table__title">${val.nama_produk}</span>
                            <span class="table__information">Harga :</span><span class="table__data">
                                ${number_formatter_to_currency(parseInt(val.harga_produk))} / ${val.besaran_produk}
                            </span>
                            <span class="table__information">Jumlah :</span><span class="table__data">${val.jumlah_produk}</span>
                            <span class="table__information">Bayaran :</span><span class="table__data">
                                ${number_formatter_to_currency(parseInt(val.harga_produk*val.jumlah_produk))}
                            </span></li>`;
                    });

                    const tr = document.createElement('tr');
                    tr.classList.add('table__row-detail');
                    tr.classList.add('table__row-detail--show');
                    tr.innerHTML = `<td colspan="7"><ul>${li}</ul></td>`;
                    target.parentElement.parentElement.after(tr);
                }
            })
            .catch(error => {
                console.error(error);
            });
        }
    }
});

document.querySelector('a#remove-transaction').addEventListener('click', e => {
    e.preventDefault();

    const checkboxs_checked = document.querySelectorAll('input[type="checkbox"][name="transaction_id"]:checked');
    // if not found input checkbox checklist
    if (checkboxs_checked.length === 0) {
        return false;
    }

    // show modal
    show_modal(modal, modal_content);
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

// show password
document.querySelector('.modal a#show-password').addEventListener('click', show_password);

// remove transaction
document.querySelector('a#remove-transaction-in-db').addEventListener('click', e => {
    e.preventDefault();

    // reset form message
    const small = modal_content.querySelector('small.form-message');
    if (small !== null) {
        small.remove();
    }

    // generate data
    let data = '';

    const csrf_name = table.dataset.csrfName;
    const csrf_value = table.dataset.csrfValue;
    const password = modal_content.querySelector('input[name="password"]').value;
    data += `${csrf_name}=${csrf_value}&password=${password}`;

    let transaction_ids = '';
    const checkboxs_checked = document.querySelectorAll('input[type="checkbox"][name="transaction_id"]:checked');
    checkboxs_checked.forEach((val, index) => {
        // if last checkbox
        if (index === checkboxs_checked.length-1) {
            transaction_ids += val.value;
        } else {
            transaction_ids += val.value+',';
        }
    });
    data += `&transaction_ids=${transaction_ids}`;

    // get smallest create time in table
    const all_checkboxs = document.querySelectorAll('input[type="checkbox"][name="transaction_id"]');
    data += `&smallest_create_time=${all_checkboxs[all_checkboxs.length-1].dataset.createTime}`;

    // if dataset type-show and dataset date-range exists in table tag
    if (table.dataset.typeShow !== undefined && table.dataset.dateRange !== undefined) {
        data += `&date_range=${table.dataset.dateRange}`;
    }

    // loading
    e.target.classList.add('btn--disabled');
    e.target.nextElementSibling.classList.remove('d-none');

    fetch('/admin/hapus_transaksi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: data
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

        // if remove transaction success
        if (json.status === 'success') {
            checkboxs_checked.forEach(val => {
                // if exists detail transaction in table
                const table_row_detail = val.parentElement.parentElement.parentElement.nextElementSibling;
                if (table_row_detail !== null && table_row_detail.classList.contains('table__row-detail')) {
                    // remove detail transaction
                    table_row_detail.remove();
                }

                // remove transaction checklist
                val.parentElement.parentElement.parentElement.remove();
            });

            // if longer transaction exists
            if (json.longer_transactions.length > 0) {
                json.longer_transactions.forEach((t, i) => {
                    const tr = document.createElement('tr');

                    // if i is odd number
                    if ((i+1)%2 !== 0) {
                        tr.classList.add('table__row-odd');
                    }
                    let td = '<td width="10">';

                    // if transaction is allow for delete
                    if (t.permission_delete === true) {
                        td += `<div class="form-check">
                                <input type="checkbox" name="transaction_id" data-create-time="${t.waktu_buat}" class="form-check-input" value="${t.transaksi_id}">
                            </div>`;
                    }

                    td += `</td>
                        <td width="10"><a href="#" id="show-transaction-detail" data-transaction-id="${t.transaksi_id}" title="Lihat detail transaksi"><svg xmlns="http://www.w3.org/2000/svg" width="21" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg></a></td>

                        <td>${t.product_total||0}</td>
                        <td>${number_formatter_to_currency(parseInt(t.payment_total||0))}</td>`;

                    if (t.status_transaksi === 'selesai') {
                        td += '<td><span class="text-green">Selesai</span></td>';
                    } else {
                        td += '<td><span class="text-red">Belum</span></td>';
                    }

                    td += `<td>${t.nama_lengkap}</td><td>${t.indo_create_time}</td></tr>`;

                    // inner td to tr
                    tr.innerHTML = td;
                    // append tr to tbody
                    table.querySelector('tbody').append(tr);
                });
            }

            const count_transaction_in_table = table.querySelectorAll('tbody tr').length;
            // if transaction total = 0
            if (json.transaction_total === 0) {
                // inner html message
                table.querySelector('tbody').innerHTML = `<tr class="table__row-odd"><td colspan="7">Transaksi tidak ada.</td></tr>`;

                // if dataset type-show and dataset date-range exists in table tag
                if (table.dataset.typeShow !== undefined && table.dataset.dateRange !== undefined) {
                    // show result status
                    result_status.innerText = '0 Total transaksi hasil pencarian';
                } else {
                    // show result status
                    result_status.innerText = '0 Total transaksi';
                }

            } else {
                // if dataset type-show and dataset date-range exists in table tag
                if (table.dataset.typeShow !== undefined && table.dataset.dateRange !== undefined) {
                    // show result status
                    result_status.innerText = `1 - ${count_transaction_in_table} dari ${json.transaction_total} Total transaksi hasil pencarian`;
                } else {
                    // show result status
                    result_status.innerText = `1 - ${count_transaction_in_table} dari ${json.transaction_total} Total transaksi`;
                }
            }

            // if total transaction in table < transaction limit and limit message exists
            const limit_message = document.querySelector('span#limit-message');
            if (count_transaction_in_table < json.transaction_limit && limit_message !== null) {
                limit_message.remove();
            }
        }
        // else if password sign in user is wrong
        else if (json.status === 'wrong_password') {
            const small = document.createElement('small');
            small.classList.add('form-message');
            small.classList.add('form-message--danger');
            small.innerText = json.message;

            // append message to modal
            modal_content.querySelector('div.modal__body').append(small);
        }
        // else if fail remove transaction
        else if (json.status === 'fail') {
            const alert = create_alert_node(['alert--warning', 'mb-3'], json.message);
            // append alert to before div.main__box element
            document.querySelector('main.main > div').insertBefore(alert, document.querySelector('div.main__box'));

            // reset input checkboxs checked
            checkboxs_checked.forEach(val => {
                val.checked = false;
            });
        }

        if (json.status === 'success' || json.status === 'fail') {
            // hide modal
            hide_modal(modal, modal_content);
            // reset modal
            modal_content.querySelector('input[name="password"]').value = '';
        }
    })
    .catch(error => {
        console.error(error);
    });
});

// export transactions to excel
export_transaction_excel.addEventListener('click', e => {
    e.preventDefault();

    // generate data
    let data = '';

    const csrf_name = table.dataset.csrfName;
    const csrf_value = table.dataset.csrfValue;
    data += `${csrf_name}=${csrf_value}`;

    // if dataset type-show and dataset date-range exists in table tag
    if (table.dataset.typeShow !== undefined && table.dataset.dateRange !== undefined) {
        data += `&date_range=${table.dataset.dateRange}`;
    }

    // loading
    export_transaction_excel.nextElementSibling.classList.remove('d-none');
    // disabled button search
    search_transaction.classList.add('btn--disabled');
    // disabled action in table
    const table_loading = table.parentElement.nextElementSibling;
    table_loading.querySelector('.loading').classList.add('d-none');
    table_loading.classList.remove('d-none');

    fetch('/admin/ekspor_transaksi_ke_excel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: data
    })
    .finally(() => {
        // loading
        export_transaction_excel.nextElementSibling.classList.add('d-none');
        // disabled button search
        search_transaction.classList.remove('btn--disabled');
        // disabled action in table
        const table_loading = table.parentElement.nextElementSibling;
        table_loading.querySelector('.loading').classList.remove('d-none');
        table_loading.classList.add('d-none');
    })
    .then(response => {
        return response.json();
    })
    .then(json => {
        // set new csrf hash to table tag
        if (json.csrf_value !== undefined) {
            table.dataset.csrfValue = json.csrf_value;
        }

        // if export transactions success
        if (json.status === 'success') {
             const alert = create_alert_node(['alert--success', 'mb-3'], json.message);
            // append alert to before div.main__box element
            document.querySelector('main.main > div').insertBefore(alert, document.querySelector('div.main__box'));
        }
        // else if export transactions fail
        else if (json.status === 'fail') {
            const alert = create_alert_node(['alert--warning', 'mb-3'], json.message);
            // append alert to before div.main__box element
            document.querySelector('main.main > div').insertBefore(alert, document.querySelector('div.main__box'));
        }
    })
    .catch(error => {
        console.error(error);
    });
});
