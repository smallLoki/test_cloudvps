var listUrls = [];
var domain = window.location.host;
if(window.location.toString().indexOf('urls')>0) {
    window.onload = getUrls();
}
var elDomain = document.getElementById('domain') ?? false;

if (elDomain) {
    elDomain.innerText = domain+'/s/';
}

function getUrls() {
    postData('/api/urls/getUrls', {})
        .then((result) => {
            setViewUrls(result['listUrls']);
        });
}

function setViewUrls(list) {
    listUrls = list
    let listHTML = "";
    list.map((data, index) => {
        listHTML +=`
        <tr>
            <th scope="row">${index + 1}</th>
            <td>${data.id}</td>
            <td><a href="${data.url}" class="url">${data.url}</a></td>
            <td>${data.short_url}</td>
            <td>${data.count}</td>
            <td>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-outline-success" onclick="setEditData(${index})" data-bs-toggle="modal" data-bs-target="#editURL">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                        </svg>
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteUrl(${data.id})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>`
    });
    document.getElementById('tbodyUrls').innerHTML = listHTML;
}

function sendUrl() {
    const urlForm = document.forms.urlForm;
    const shortUrl = urlForm.shortUrl.value;
    const url = urlForm.url.value;
    if (isValidUrl(url)) {
        let data = {
            url,
            shortUrl
        };
        postData('/api/urls/createUrl', data)
            .then((result) => {
                const text_result = document.getElementById('text_result');
                text_result.classList.remove("shadow-block");

                text_result.innerText = result.error ? result.message : result.shortUrl
            });
    } else {
        urlForm.url.classList.add("is-invalid");
        //
    }
}

function onChangeUrl(e) {
    document.forms.url.classList.remove("is-invalid");
}

function isValidUrl(url) {
    var objRE = /((^https?:\/\/))?[a-z0-9~_\-\.]+\.[a-z]{2,9}(\/|:|\?[!-~]*)?$/i;
    return objRE.test(url);
}

async function postData(url = '', data = {})  {
    const response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json; charset=utf-8'},
        body: JSON.stringify(data)
    });
    return response.json();
}

function deleteUrl(id) {
    postData('/api/urls/deleteUrl', {id})
        .then((result) => {
            if (!result['error'] && result['result']) {
                getUrls();
            } else {
                //
            }
        });
}

function setEditData(index) {
    let data = listUrls[index];
    const sUrl = data.short_url.replace(window.location.protocol + '//', '').replace(window.location.host + '/s/', '');
    const domain_ = domain+'/s/';
    document.getElementById('formEditUrl').innerHTML = `
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="editURLLabel">Редактирование ссылки #${data.id}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <input type="text" name="url" class="form-control" placeholder="Введите ссылку"
                 onchange="onChangeUrl(event)" value="${data.url}" required>
            </div>
            <div class="mb-3">
                <label for="half-link" class="form-label">Вторая половина адреса</label>
                <div class="input-group">
                    <span class="input-group-text">${domain_}</span>
                    <input type="text" class="form-control" name="shortUrl" id="half-link" placeholder="Пример: market-link"
                        value="${sUrl}">
                </div>
            </div>
            <div class="alert alert-danger shadow-block" role="alert" id="text_result">

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModal">Close</button>
            <button type="button" class="btn btn-primary" onclick="editDataUrl(${data.id})">Save changes</button>
        </div>
    `;
}

function editDataUrl(id) {
    const urlForm = document.forms.urlForm;
    const shortUrl = urlForm.shortUrl.value;
    const url = urlForm.url.value;
    if (isValidUrl(url)) {
        let data = {
            id,
            url,
            shortUrl
        };
        postData('/api/urls/updateUrl', data)
            .then((result) => {
                const text_result = document.getElementById('text_result');
                text_result.classList.remove("shadow-block");
                if (!result.error) {
                    document.getElementById('closeModal').click();
                } else {
                    text_result.innerText = result.message
                }
            });
    } else {
        urlForm.url.classList.add("is-invalid");
    }
}