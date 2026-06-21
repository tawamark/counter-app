package br.com.counter.mobile

import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path

interface ApiService {
    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<ApiResponse<LoginData>>

    @POST("logout")
    suspend fun logout(): Response<ApiResponse<Any>>

    @GET("mobile/summary")
    suspend fun summary(): Response<ApiResponse<SummaryData>>

    @GET("inventory-counts")
    suspend fun counts(): Response<ApiResponse<List<InventoryCountData>>>

    @GET("inventory-counts/{id}/items")
    suspend fun countItems(@Path("id") id: Int): Response<ApiResponse<List<CountItemData>>>

    @POST("inventory-counts/{id}/sync")
    suspend fun syncItems(@Path("id") id: Int, @Body request: SyncRequest): Response<ApiResponse<InventoryCountData>>
}
