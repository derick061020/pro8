import { p as provideApi } from "./index.2bb13d6f.js";
const setRestaurantFavorite = async (itemId, favorite) => {
  try {
    const payload = { id: itemId };
    const { data } = await provideApi().post("/restaurant/items/restaurant-favorite", payload);
    return data;
  } catch (err) {
    console.error("Error toggling restaurant favorite", err);
    throw err;
  }
};
const getProductsStock = async () => {
  try {
    const { data } = await provideApi().get("/restaurant/items/stock");
    return data;
  } catch (err) {
    console.error("Error fetching products stock", err);
    throw err;
  }
};
const RestaurantService = { setRestaurantFavorite, getProductsStock };
const getProductsByCommandStatus = async (mesaId) => {
  try {
    const { data } = await provideApi().get(`/restaurant/command-status/served/${mesaId}`);
    return data;
  } catch (err) {
    console.error("Error fetching products by command status", err);
    throw err;
  }
};
const CommandStatusService = { getProductsByCommandStatus };
export { CommandStatusService as C, RestaurantService as R };
