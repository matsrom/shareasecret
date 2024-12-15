window.aesEncrypt = async function aesEncrypt(data, password, difficulty = 10) {
    const hashKey = await grindKey(password, difficulty);
    const iv = await getIv(password, data);

    const key = await window.crypto.subtle.importKey(
        "raw",
        hashKey,
        {
            name: "AES-GCM",
        },
        false,
        ["encrypt"]
    );

    const encrypted = await window.crypto.subtle.encrypt(
        {
            name: "AES-GCM",
            iv,
            tagLength: 128,
        },
        key,
        new TextEncoder("utf-8").encode(data)
    );

    const result = Array.from(iv).concat(Array.from(new Uint8Array(encrypted)));

    return base64Encode(new Uint8Array(result));
};

window.aesEncryptFile = async function aesEncryptFile(
    file,
    password,
    difficulty = 10
) {
    const reader = new FileReader();
    return new Promise((resolve, reject) => {
        reader.onload = async function (e) {
            const data = e.target.result;
            const hashKey = await grindKey(password, difficulty);
            const iv = await getIv(password, data);

            const key = await window.crypto.subtle.importKey(
                "raw",
                hashKey,
                {
                    name: "AES-GCM",
                },
                false,
                ["encrypt"]
            );
            const encrypted = await window.crypto.subtle.encrypt(
                { name: "AES-GCM", iv },
                key,
                data
            );

            const result = new Uint8Array(iv.length + encrypted.byteLength);
            result.set(iv);
            result.set(new Uint8Array(encrypted), iv.length);

            const base64String = base64Encode(result);
            resolve(base64String);
        };

        reader.onerror = reject;
        reader.readAsArrayBuffer(file);
    });
};

// Función para derivar clave usando PBKDF2
window.deriveKey = async function deriveKey(
    password,
    salt = new Uint8Array(16)
) {
    const encoder = new TextEncoder();
    const passwordBuffer = encoder.encode(password);

    // Importar la contraseña como material de clave
    const baseKey = await window.crypto.subtle.importKey(
        "raw",
        passwordBuffer,
        "PBKDF2",
        false,
        ["deriveBits"]
    );

    // Derivar clave usando PBKDF2
    const derivedKey = await window.crypto.subtle.deriveBits(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: 100000,
            hash: "SHA-256",
        },
        baseKey,
        256
    );

    return derivedKey;
};

window.aesDecrypt = async function aesDecrypt(
    ciphertext,
    password,
    difficulty = 10
) {
    const ciphertextBuffer = Array.from(base64Decode(ciphertext));
    const hashKey = await grindKey(password, difficulty);

    const key = await window.crypto.subtle.importKey(
        "raw",
        hashKey,
        {
            name: "AES-GCM",
        },
        false,
        ["decrypt"]
    );

    const decrypted = await window.crypto.subtle.decrypt(
        {
            name: "AES-GCM",
            iv: new Uint8Array(ciphertextBuffer.slice(0, 12)),
            tagLength: 128,
        },
        key,
        new Uint8Array(ciphertextBuffer.slice(12))
    );

    return new TextDecoder("utf-8").decode(new Uint8Array(decrypted));
};

window.aesDecryptFile = async function aesDecryptFile(
    encryptedBase64,
    password,
    originalFilename,
    difficulty = 10
) {
    const encryptedData = base64Decode(encryptedBase64);
    const iv = encryptedData.slice(0, 12); // Extraer IV
    const data = encryptedData.slice(12); // Extraer datos encriptados
    const hashKey = await grindKey(password, difficulty);

    const key = await window.crypto.subtle.importKey(
        "raw",
        hashKey,
        {
            name: "AES-GCM",
        },
        false,
        ["decrypt"]
    );

    try {
        const decrypted = await window.crypto.subtle.decrypt(
            {
                name: "AES-GCM",
                iv: iv,
                tagLength: 128,
            },
            key,
            data
        );

        // Crear un Blob a partir de los datos desencriptados
        const blob = new Blob([decrypted], {
            type: "application/octet-stream",
        });

        // Crear un enlace de descarga
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = originalFilename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Error al desencriptar el archivo:", error);
    }
};

function grindKey(password, difficulty) {
    return pbkdf2(
        password,
        password + password,
        Math.pow(2, difficulty),
        32,
        "SHA-256"
    );
}

function getIv(password, data) {
    const randomData = base64Encode(
        window.crypto.getRandomValues(new Uint8Array(12))
    );
    return pbkdf2(
        password + randomData,
        data + new Date().getTime().toString(),
        1,
        12,
        "SHA-256"
    );
}

function base64Encode(u8) {
    return btoa(String.fromCharCode.apply(null, u8));
}

function base64Decode(str) {
    return new Uint8Array(
        atob(str)
            .split("")
            .map((c) => c.charCodeAt(0))
    );
}

async function pbkdf2(message, salt, iterations, keyLen, algorithm) {
    const msgBuffer = new TextEncoder("utf-8").encode(message);
    const msgUint8Array = new Uint8Array(msgBuffer);
    const saltBuffer = new TextEncoder("utf-8").encode(salt);
    const saltUint8Array = new Uint8Array(saltBuffer);

    const key = await crypto.subtle.importKey(
        "raw",
        msgUint8Array,
        {
            name: "PBKDF2",
        },
        false,
        ["deriveBits"]
    );

    const buffer = await crypto.subtle.deriveBits(
        {
            name: "PBKDF2",
            salt: saltUint8Array,
            iterations: iterations,
            hash: algorithm,
        },
        key,
        keyLen * 8
    );

    return new Uint8Array(buffer);
}
