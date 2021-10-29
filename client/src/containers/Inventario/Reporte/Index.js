import React from 'react'
import Typography from '@material-ui/core/Typography';
import GetAppOutlinedIcon from '@material-ui/icons/GetAppOutlined';
import AddIcon from '@material-ui/icons/Add';
import SearchIcon from '@material-ui/icons/Search';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import InputAdornment from '@material-ui/core/InputAdornment';
import DesktopWindowsIcon from '@material-ui/icons/DesktopWindows';
import EqualizerIcon from '@material-ui/icons/Equalizer';
import Avatar from '@material-ui/core/Avatar';
import Initializer from '../../../store/Initializer'
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import { LocalizationTable, TableIcons, removeAccent } from '../../../utils/table.js'
import MaterialTable from "material-table";
import { Grid } from '@material-ui/core';
import { obtenerTodos } from '../../../utils/API/usuarios.js';
import Crear from './componentes/Crear'
import Eliminar from './componentes/Eliminar'
import Filtro from './componentes/Filtro'
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import VisibilityOutlinedIcon from '@material-ui/icons/VisibilityOutlined';
import { downloadFiles } from '../../../utils/API/reporte';
export default function Sistemas(props) {
    const initializer = React.useContext(Initializer);
    const [tipo, setTipo] = React.useState("")

    const [data, setData] = React.useState([])
    const [open, setOpen] = React.useState(false)
    const [open2, setOpen2] = React.useState(false)
    const [selected, setSelected] = React.useState(null)
    const [selected2, setSelected2] = React.useState(null)
    const [openFilter, setOpenFilter] = React.useState(false)
    const [filtro, setFiltro] = React.useState('')

    React.useEffect(() => {
        if (initializer.usuario != null) {
            obtenerTodos(setData, initializer)
        }
    }, [initializer.usuario])
    const carga = () => {
        obtenerTodos(setData, initializer)
        setSelected(null)
        setSelected2(null)
    }
    const total = () => {
        let tot = 0
        data.map((e) => {
            tot += e.evaluaciones
        })
        return tot
    }
    const reporte = () => {
        downloadFiles({tipo:tipo,filtro:filtro},initializer)
    }
    return (
        <Grid container spacing={2}>
            <Crear sistema={selected} setSelected={setSelected} setOpen={setOpen} open={open} carga={carga} />
            <Eliminar sistema={selected2} setOpen={setOpen2} open={open2} carga={carga} />
            <Filtro setOpen={setOpenFilter} open={openFilter} />

            <Grid item xs={12} md={12} style={{ display: 'flex', justifyContent: 'space-between' }}>
                <Typography variant="h5" >
                    Reporte
                </Typography>

            </Grid>
            <Grid item xs={12} md={12}>

            <Grid  xs={12} md={12}>
                <Card >
                    <CardContent>
                        <Grid container spacing={2}>
                    <Grid item xs={12} md={12}>
                        <FormControl variant="outlined"    style={{ width: '100%' }} >
                            <InputLabel id="demo-simple-select-outlined-label"   style={{ width: '100%' }} >Seleccione el reporte</InputLabel>
                            <Select
                                labelId="demo-simple-select-outlined-label"
                                id="demo-simple-select-outlined"
                                value={tipo}
                                style={{ width: '100%' }}
                                onChange={(e) => {
                                    setTipo(e.target.value)
                                    setFiltro("")
                                }}
                                label="Seleccione el reporte"
                            >
                                <MenuItem value="">
                                    <em>Seleccione una opción</em>
                                </MenuItem>
                                <MenuItem value={'facturas'}>Facturas</MenuItem>
                                <MenuItem value={'kardex'}>Kardex</MenuItem>
                                <MenuItem value={'clientes'}>Clientes</MenuItem>
                                <MenuItem value={'productos'}>Productos</MenuItem>

                            </Select>
                            </FormControl>
                            </Grid>
                            {tipo=='facturas'&&(
                            <Grid item xs={12}>    <TextField
                        variant="outlined"
                        style={{ width:'100%' }}
                        type="number"
                        label="Número"
                        value={filtro}
                        onChange={(e) => setFiltro(e.target.value)}

                    /></Grid>)
                    }
                      </Grid>
                    </CardContent>
                    <CardActions>
                        <Button variant="contained" disabled={tipo==""} startIcon={<GetAppOutlinedIcon />} size="small" color="primary" onClick={reporte}>Descargar reporte</Button>
                    </CardActions>
                </Card>
                </Grid>

                </Grid>


        </Grid>
    )
}
