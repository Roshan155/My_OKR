// ✅ REGISTER
document.getElementById("registerForm")?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const team_id = parseInt(document.getElementById("team_id").value.trim());
  const role = document.getElementById("role").value;

  if (!name || !email || !password || isNaN(team_id)) {
    alert("❌ All fields are required.");
    return;
  }

  const payload = { name, email, password, team_id, role };

  try {
    const res = await fetch("http://localhost/MyOKR/backend/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const result = await res.json();
    if (result.status === "success" || result.success) {
      alert("✅ Registered successfully!");
      window.location.href = "create.html"; // Redirect to create page directly
    } else {
      alert("❌ " + (result.message || "Registration failed."));
    }
  } catch (err) {
    alert("❌ Registration failed.");
    console.error("Registration error:", err);
  }
});

// 🔐 LOGIN
document.getElementById("loginForm")?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!email || !password) {
    alert("❌ Email and password required.");
    return;
  }

  const payload = { email, password };

  try {
    const res = await fetch("http://localhost/MyOKR/backend/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const result = await res.json();
    if (result.status === "success" || result.success) {
      localStorage.setItem("user_id", result.user_id);
      alert("✅ Login successful!");
      window.location.href = "create.html";
    } else {
      alert("❌ " + (result.message || "Login failed."));
    }
  } catch (err) {
    alert("❌ Login failed.");
    console.error("Login error:", err);
  }
});

// ➕ CREATE OKR
const okrForm = document.getElementById("okrForm");
if (okrForm) {
  okrForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const objective = document.getElementById("objective").value.trim();
    const key_results = document.getElementById("key_results").value.trim();
    const team_id = parseInt(document.getElementById("team_id").value);
    const user_id = parseInt(localStorage.getItem("user_id"));

    if (!objective || !key_results || isNaN(user_id) || isNaN(team_id)) {
      alert("❌ Please fill in all required fields.");
      return;
    }

    const payload = { objective, key_results, user_id, team_id, progress: 0 };

    try {
      const response = await fetch("http://localhost/MyOKR/backend/create.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();
      if (result.status === "success" || result.success) {
        alert("✅ OKR Created Successfully!");
        okrForm.reset();
        loadOKRs();
      } else {
        alert("❌ Error: " + (result.message || "Unknown error."));
      }
    } catch (err) {
      alert("❌ Request failed.");
      console.error("Create Error:", err);
    }
  });
}

// 📥 READ OKRs
async function loadOKRs() {
  try {
    const response = await fetch("http://localhost/MyOKR/backend/read.php");
    const result = await response.json();

    const okrs = result.data || [];
    const okrList = document.getElementById("okrList");
    okrList.innerHTML = "";

    if (!Array.isArray(okrs) || okrs.length === 0) {
      okrList.innerHTML = "<p>No OKRs found.</p>";
      return;
    }

    okrs.forEach((okr) => {
      const progressValue = okr.progress || 0;

      const okrItem = document.createElement("div");
      okrItem.className = "border p-4 rounded shadow bg-white space-y-2";

      okrItem.innerHTML = `
        <h3 class="text-xl font-bold text-blue-600">${okr.objective}</h3>
        <p><strong>Key Results:</strong> ${okr.key_results}</p>
        <p><strong>User ID:</strong> ${okr.user_id}</p>
        <p><strong>Team ID:</strong> ${okr.team_id}</p>
        <div class="mt-3">
          <label class="font-semibold text-sm text-gray-700">Progress: ${progressValue}%</label>
          <div class="w-full bg-gray-300 rounded h-4 mt-1">
            <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded transition-all" style="width: ${progressValue}%"></div>
          </div>
        </div>
        <div class="mt-3 space-x-2">
          <button onclick="editOKR(${okr.id})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">✏️ Edit</button>
          <button onclick="deleteOKR(${okr.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">🗑️ Delete</button>
        </div>
      `;

      okrList.appendChild(okrItem);
    });
  } catch (err) {
    console.error("❌ Error loading OKRs:", err);
    document.getElementById("okrList").innerHTML = "<p class='text-red-500'>❌ Failed to load OKRs.</p>";
  }
}

// ✏️ UPDATE OKR
async function editOKR(id) {
  const objective = prompt("Enter updated objective:");
  const key_results = prompt("Enter updated key results:");
  const progress = prompt("Enter progress (0-100):");

  if (!objective || !key_results || isNaN(parseInt(progress))) {
    alert("❌ Invalid input.");
    return;
  }

  const payload = { id, objective, key_results, progress: parseInt(progress) };

  try {
    const response = await fetch("http://localhost/MyOKR/backend/update.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const result = await response.json();
    if (result.status === "success" || result.success) {
      alert("✅ OKR Updated!");
      loadOKRs();
    } else {
      alert("❌ Update failed: " + result.message);
    }
  } catch (err) {
    alert("❌ Error updating OKR.");
    console.error(err);
  }
}

// 🗑️ DELETE OKR
async function deleteOKR(id) {
  if (!confirm("Are you sure you want to delete this OKR?")) return;

  const payload = { id };

  try {
    const response = await fetch("http://localhost/MyOKR/backend/delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const result = await response.json();
    if (result.status === "success" || result.success) {
      alert("✅ OKR Deleted!");
      loadOKRs();
    } else {
      alert("❌ Delete failed: " + result.message);
    }
  } catch (err) {
    alert("❌ Error deleting OKR.");
    console.error(err);
  }
}

// 🌀 INIT: Load OKRs on page load
if (document.getElementById("okrList")) {
  loadOKRs();
}
