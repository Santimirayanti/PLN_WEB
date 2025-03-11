<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tracker - PLN Web App</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 p-6">
        <button onclick="window.location.href='approver_home.html'"
            class="mb-4 px-4 py-2 bg-gray-500 text-white rounded">Kembali</button>

        <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan</h1>
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-[#009DDB] text-white">
                <tr>
                    <th class="p-4">User</th>
                    <th class="p-4">Nama Pekerjaan</th>
                    <th class="p-4">Approved</th>
                    <th class="p-4">Photo</th>
                    <th class="p-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="p-4 text-center align-middle">
                        Elisa Manopo
                    </td>
                    <td class="p-4">Nama Pekerjaan</td>
                    <td class="p-4">
                        <select
                            class="approval-dropdown w-full p-2 border rounded">
                            <option value="Pending">Pending</option>
                            <option value="Reject">Reject</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </td>
                    <td class="p-4">
                        <div class="text-center font-semibold mb-2">Pengecekan
                            Dokumen DP3, JSA, WP</div>
                        <div class="flex gap-2 imageContainer">
                            <img src="../../assets/img/code.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                            <img src="../../assets/img/logo_pln.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                            <img src="../../assets/img/code.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                        </div>
                    </td>
                    <td class="p-4 text-center align-middle">
                        <button
                            class="px-4 py-3 bg-green-500 text-white rounded">Kirim</button>
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="p-4 text-center align-middle">
                        Marco Simick
                    </td>
                    <td class="p-4">Nama Pekerjaan</td>
                    <td class="p-4">
                        <select
                            class="approval-dropdown w-full p-2 border rounded">
                            <option value="Pending">Pending</option>
                            <option value="Reject">Reject</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </td>
                    <td class="p-4">
                        <div class="text-center font-semibold mb-2">Pantauan
                            Hotspot Sebelum Manuver</div>
                        <div class="flex gap-2 imageContainer">
                            <img src="../../assets/img/logo_pln.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                            <img src="../../assets/img/code.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                            <img src="../../assets/img/code.png"
                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                onclick="openModal(this)">
                        </div>
                    </td>
                    <td class="p-4 text-center align-middle">
                        <button
                            class="px-4 py-3 bg-green-500 text-white rounded">Kirim</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div id="modal"
            class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-75 flex items-center justify-center hidden">
            <button onclick="prevImage()"
                class="absolute left-4 text-white text-4xl font-bold">&larr;</button>
            <img id="modalImg"
                class="max-w-full max-h-full transition-transform duration-300">
            <button onclick="nextImage()"
                class="absolute right-4 text-white text-4xl font-bold">&rarr;</button>
            <button onclick="closeModal()"
                class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
        </div>

        <script>
            let images = [];
            let currentIndex = 0;

            function openModal(imgElement) {
                let row = imgElement.closest("tr"); // Ambil baris tempat gambar berada
                images = Array.from(row.querySelectorAll(".imageContainer img")).map(img => img.src);
                currentIndex = images.indexOf(imgElement.src);
                document.getElementById("modalImg").src = images[currentIndex];
                document.getElementById("modal").classList.remove("hidden");
            }

            function closeModal() {
                document.getElementById("modal").classList.add("hidden");
            }

            function prevImage() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                document.getElementById("modalImg").src = images[currentIndex];
            }

            function nextImage() {
                currentIndex = (currentIndex + 1) % images.length;
                document.getElementById("modalImg").src = images[currentIndex];
            }

            document.addEventListener("keydown", function(event) {
                if (event.key === "ArrowLeft") prevImage();
                if (event.key === "ArrowRight") nextImage();
                if (event.key === "Escape") closeModal();
            });
        </script>
    </body>
</html>