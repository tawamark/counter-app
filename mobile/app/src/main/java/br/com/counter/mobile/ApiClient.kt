package br.com.counter.mobile

import android.content.Context
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object ApiClient {
    fun create(context: Context, sessionManager: SessionManager): ApiService {
        val logging = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BASIC
        }

        val client = OkHttpClient.Builder()
            .addInterceptor { chain ->
                val token = sessionManager.token()
                val builder = chain.request().newBuilder()
                    .addHeader("Accept", "application/json")

                if (!token.isNullOrBlank()) {
                    builder.addHeader("Authorization", "Bearer $token")
                }

                chain.proceed(builder.build())
            }
            .addInterceptor(logging)
            .build()

        return Retrofit.Builder()
            .baseUrl(context.getString(R.string.api_base_url))
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}
