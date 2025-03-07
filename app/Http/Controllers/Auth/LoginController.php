use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // ... other methods ...

    protected function authenticated(Request $request, $user)
    {
        if ($request->user()->email === 'talentohumanonacional@bicicletasmilan.com') {
            return redirect('/permisosmilan');
        }
        return redirect('/permisosmilan/create');
    }
}