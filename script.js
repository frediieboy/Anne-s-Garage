
function calculateServiceCost() {
    const services = document.querySelectorAll(
        'input[name="services[]"]:checked'
    );

    let total = 0;
    let count = 0;

    const selectedList = document.getElementById("selectedServicesList");
    selectedList.innerHTML = "";

    services.forEach(function(service) {
        total += Number(service.dataset.price);
        count++;

        const listItem = document.createElement("li");
        listItem.textContent =
            service.value + " - KSh " +
            Number(service.dataset.price).toLocaleString("en-KE");

        selectedList.appendChild(listItem);
    });

    if (count === 0) {
        const listItem = document.createElement("li");
        listItem.textContent = "No services selected yet.";
        selectedList.appendChild(listItem);
    }

    document.getElementById("estimatedTotal").textContent =
        "KSh " + total.toLocaleString("en-KE");

    document.getElementById("serviceCount").textContent = count;
}

function toggleOtherServices() {
    const fullServiceCheckbox = document.getElementById("fullServiceCheckbox");
    const otherServiceCheckboxes = document.querySelectorAll(
        'input[name="services[]"]:not(#fullServiceCheckbox)'
    );

    if (fullServiceCheckbox.checked) {
        otherServiceCheckboxes.forEach(function(checkbox) {
            checkbox.disabled = true;
            checkbox.checked = false; 
        });
    } else {
        otherServiceCheckboxes.forEach(function(checkbox) {
            checkbox.disabled = false;
        });
    }
    calculateServiceCost(); 
}

document.querySelectorAll('input[name="services[]"]').forEach(
    function(service) {
        service.addEventListener("change", calculateServiceCost);
    }
);

const fullServiceCheckbox = document.getElementById("fullServiceCheckbox");
if (fullServiceCheckbox) {
    fullServiceCheckbox.addEventListener("change", toggleOtherServices);
}

function validateServiceForm() {
    const name = document.getElementById("customer_name").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const email = document.getElementById("email").value.trim();
    const registration = document.getElementById("registration").value.trim();
    const year = Number(document.getElementById("year").value);
    const serviceDate = document.getElementById("service_date").value;

    const currentYear = new Date().getFullYear();

    if (name === "") {
        alert("Please enter your full name.");
        return false;
    }

    if (!/^\d{10}$/.test(phone)) {
        alert("Phone number must contain exactly 10 digits.");
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (registration === "") {
        alert("Please enter your vehicle registration number.");
        return false;
    }

    if (!Number.isInteger(year) || year < 1900 || year > currentYear) {
        alert("Please enter a reasonable year of manufacture.");
        return false;
    }

    if (serviceDate === "") {
        alert("Please select a preferred service date.");
        return false;
    }

    const selectedServices = document.querySelectorAll(
        'input[name="services[]"]:checked'
    );

    if (selectedServices.length === 0) {
        alert("Please select at least one service.");
        return false;
    }

    return true;
}

function checkVehicleAge() {
    const yearInput = document.getElementById("year");
    const message = document.getElementById("vehicleAgeMessage");

    if (yearInput.value === "") {
        message.textContent = "";
        return;
    }

    const year = Number(yearInput.value);
    const currentYear = new Date().getFullYear();
    const age = currentYear - year;

    if (year < 1900 || year > currentYear) {
        message.textContent = "Please enter a valid manufacturing year.";
    } else if (age > 15) {
        message.textContent =
            "Warning: This vehicle is over 15 years old. " +
            "Additional maintenance checks may be advisable.";
    } else {
        message.textContent =
            "Vehicle age: " + age + " years.";
    }
}

document.getElementById("year").addEventListener(
    "input",
    checkVehicleAge
);
