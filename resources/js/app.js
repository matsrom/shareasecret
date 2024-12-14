import "./bootstrap";
import "./cryptogtaphy";
import "flowbite";
import Toastify from "toastify-js";
import L from "leaflet";

window.Toastify = Toastify;

window.L = L;
if (typeof L !== "undefined") {
    console.log("Leaflet se ha importado correctamente.");
} else {
    console.error("Error: Leaflet no se ha importado.");
}
