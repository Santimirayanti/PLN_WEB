<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker - PLN Web App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <button onclick="window.location.href='home.html'" class="mb-4 px-4 py-2 bg-gray-500 text-white rounded">Kembali</button>
    
    <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan</h1>
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="px-4 py-2 bg-[#009DDB] text-white">
            <tr>
                <th class="p-4">Nama Pekerjaan</th>
                <th class="p-4">Status</th>
                <th class="p-4">Approved</th>
                <th class="p-4">Upload</th>
                <th class="p-4">Aksi</th>
            </tr>
        </thead>
        <tbody id="projectList">
            <!-- Data dari localStorage akan dimasukkan di sini -->
        </tbody>
    </table>

    <script>
        function loadProjects() {
            let projects = JSON.parse(localStorage.getItem("projects")) || [];
            const projectList = document.getElementById("projectList");
            projectList.innerHTML = "";

            projects.forEach((project, index) => {
                projectList.innerHTML += `
                    <tr class="border-b">
                        <td class="p-4">${project.name}</td>
                        <td class="p-4">
                            <select class="status-dropdown w-full p-2 border rounded" data-index="${index}">
                                <option value="Belum Dimulai" ${project.status === "Belum Dimulai" ? "selected" : ""}>Belum Dimulai</option>
                                <option value="Dalam Progress" ${project.status === "Dalam Progress" ? "selected" : ""}>Dalam Progress</option>
                                <option value="Selesai" ${project.status === "Selesai" ? "selected" : ""}>Selesai</option>
                            </select>
                        </td>
                        <td class="p-4">
                            <select class="approval-dropdown w-full p-2 border rounded" data-index="${index}">
                                <option value="Need Revision" ${project.approved === "Need Revision" ? "selected" : ""}>Need Revision</option>
                                <option value="Approved" ${project.approved === "Approved" ? "selected" : ""}>Approved</option>
                            </select>
                        </td>
                        <td class="p-4">
                            <select class="category-dropdown w-full p-2 border rounded" data-index="${index}">
                                <option value="Upload foto pelaksanaan">Upload foto pelaksanaan</option>
                                <optgroup label="Foto2 kegiatan">
                                    <option value="Pengecekan Dokumen DP3, JSA, WP">Pengecekan Dokumen DP3, JSA, WP</option>
                                    <option value="Pantauan hotpsot sebelum manuver">Pantauan hotpsot sebelum manuver</option>
                                    <option value="Manuver menggunakan SOP Berbasis foto">Manuver menggunakan SOP Berbasis foto</option>
                                    <option value="Pengecekan parameter arus-tegangan setelah pembebasan">Pengecekan parameter arus-tegangan setelah pembebasan</option>
                                    <option value="Merubah switch control di PMT dan PMS menjadi off">Merubah switch control di PMT dan PMS menjadi off</option>
                                    <option value="Pemasangan grounding">Pemasangan grounding</option>
                                    <option value="Pemasangan LOTO">Pemasangan LOTO</option>
                                    <option value="Safety briefing sebelum pemeliharaan">Safety briefing sebelum pemeliharaan</option>
                                    <option value="Kegiatan pemeliharaan (penggunaan checklist)">Kegiatan pemeliharaan (penggunaan checklist)</option>
                                    <option value="Pelepasan grounding">Pelepasan grounding</option>
                                </optgroup>
                            </select>
                            <input type="file" class="file-upload mt-2" data-index="${index}" onchange="previewFile(event, ${index})">
                            <div id="filePreview-${index}" class="mt-2"></div>
                        </td>
                        <td class="p-4">
                            <button onclick="removeFile(${index})" class="mt-2 px-4 py-1 bg-red-500 text-white rounded">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        }

        function previewFile(event, index) {
            const file = event.target.files[0];
            const filePreview = document.getElementById(`filePreview-${index}`);
            
            if (file) {
                const fileURL = URL.createObjectURL(file);
                filePreview.innerHTML = file.type.startsWith("image") ? `<img src="${fileURL}" class="w-20 h-20 object-cover rounded" />` : `<a href="${fileURL}" target="_blank" class="text-blue-500 underline">Lihat File</a>`;
            }
        }

        function removeFile(index) {
            document.querySelector(`.file-upload[data-index='${index}']`).value = "";
            document.getElementById(`filePreview-${index}`).innerHTML = "";
        }

        document.addEventListener("DOMContentLoaded", loadProjects);
    </script>
</body>
</html>
