package br.com.counter.mobile

import android.content.Context

class SessionManager(context: Context) {
    private val preferences = context.getSharedPreferences("counter_session", Context.MODE_PRIVATE)

    fun save(token: String, name: String, email: String, role: String) {
        preferences.edit()
            .putString("token", token)
            .putString("name", name)
            .putString("email", email)
            .putString("role", role)
            .apply()
    }

    fun token(): String? {
        return preferences.getString("token", null)
    }

    fun userName(): String {
        return preferences.getString("name", "") ?: ""
    }

    fun userEmail(): String {
        return preferences.getString("email", "") ?: ""
    }

    fun clear() {
        preferences.edit().clear().apply()
    }
}
