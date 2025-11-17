function handleFormResponse(event) {
    console.log(event)
    const xhr = event.detail.xhr;

    let data;
    try {
        data = JSON.parse(xhr.responseText);
    } catch (e) {
        alert("Invalid server response.");
        return;
    }

    if (data.status) {
        if (data.redirect) window.location.href = data.redirect;
    }
    else {
        alert(data.error ?? "Unknown error");
    }
}