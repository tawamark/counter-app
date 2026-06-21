package br.com.counter.mobile

import android.content.Context

class AuthRepository(context: Context) {
    private val sessionManager = SessionManager(context)
    private val apiService = ApiClient.create(context, sessionManager)

    suspend fun login(email: String, password: String): Result<Unit> {
        return try {
            val response = apiService.login(LoginRequest(email, password))
            val body = response.body()

            if (response.isSuccessful && body?.success == true && body.data != null) {
                sessionManager.save(body.data.token, body.data.user.name, body.data.user.email, body.data.user.role)
                Result.success(Unit)
            } else {
                Result.failure(Exception(body?.message ?: "Não foi possível realizar o login."))
            }
        } catch (exception: Exception) {
            Result.failure(Exception(exception.message ?: "Não foi possível conectar à API."))
        }
    }

    suspend fun logout() {
        runCatching {
            apiService.logout()
        }
        sessionManager.clear()
    }

    fun isLoggedIn(): Boolean {
        return !sessionManager.token().isNullOrBlank()
    }

    fun userName(): String {
        return sessionManager.userName()
    }

    fun userEmail(): String {
        return sessionManager.userEmail()
    }
}
